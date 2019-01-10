<?php
/**
* Extra feeds
*
* Add custom feeds templates
*
* @package knife-theme
* @since 1.2
* @version 1.7
* @link https://yandex.ru/support/news/feed.html
* @link https://yandex.ru/support/zen/website/rss-modify.html
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Extra_Feeds {
    /**
     * Exclude post from Yandex.Zen meta
     */
    private static $zen_exclude = '_knife-zen-exclude';


    /**
     * Republish Yandex.Zen meta
     */
    private static $zen_publish = '_knife-zen-publish';


    /**
     * Current post enclosure array
     */
    private static $enclosure = null;


    /**
     * Checkbox save nonce
     *
     * @since   1.5
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-feed-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Init custom feeds
        add_action('init', [__CLASS__, 'init_feeds']);

        // Upgrade custom feeds with hooks
        add_action('wp', [__CLASS__, 'upgrade_feeds']);

        // Alter feed main query loop
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update feed meta on save post
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Post metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 9);

        // Admin side assets
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'post') {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin scripts
        wp_enqueue_script('knife-feed-metabox', $include . '/scripts/feed-metabox.js', ['jquery'], $version);
    }


    /**
     * Add zen publish metabox
     *
     * @since 1.6
     */
    public static function add_metabox() {
        add_meta_box('knife-feed-metabox', __('Яндекс RSS', 'knife-theme'), [__CLASS__, 'display_metabox'], ['post'], 'side', 'default');
    }


    /**
     * Display feed metabox
     *
     * @since 1.6
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/feed-metabox.php');
    }


    /**
     * Save feed post meta
     */
    public static function save_meta($post_id) {
        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'fieldset')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save publish meta if not empty
        if(!empty($_REQUEST[self::$zen_publish])) {
            update_post_meta($post_id, self::$zen_publish, sanitize_text_field($_REQUEST[self::$zen_publish]));
        } else {
            delete_post_meta($post_id, self::$zen_publish);
        }

        // Save exclude meta if not empty
        if(!empty($_REQUEST[self::$zen_exclude])) {
            update_post_meta($post_id, self::$zen_exclude, 1);
        } else {
            delete_post_meta($post_id, self::$zen_exclude);
        }
    }


    /**
     * Register new feeds endpoint
     */
    public static function init_feeds() {
        // Add Yandex.Zen feed
        add_feed('zen', [__CLASS__, 'add_zen_feed']);

        // Add Yandex.Turbo feed
        add_feed('turbo', [__CLASS__, 'add_turbo_feed']);
    }


    /**
     * Upgrade custom feeds template
     *
     * @since 1.7
     */
    public static function upgrade_feeds() {
        if(is_feed(['zen', 'turbo'])) {
            // Remove post content unwanted tags
            add_filter('the_content_feed', [__CLASS__, 'clean_content'], 11);

            // Prepend lead if exists
            add_filter('the_content_feed', [__CLASS__, 'prepend_lead'], 12);

            // Upgrade post author with coauthors plugin
            add_filter('the_author', [__CLASS__, 'upgrade_author']);

            // Remove unused images attributes
            add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'image_attributes'], 10, 3);
        }

        // Only for Yandex.Zen feed
        if(is_feed('zen')) {
            // Remove embeds
            add_filter('embed_oembed_html', [__CLASS__, 'remove_embeds'], 15, 3);

            // Calculate post images
            add_filter('the_content_feed', [__CLASS__, 'add_images'], 9);

            // Remove unwanted tags
            add_filter('the_content_feed', [__CLASS__, 'remove_tags'], 10);

            // Add post category and enclosure after content
            add_action('rss2_item', [__CLASS__, 'insert_category']);
            add_action('rss2_item', [__CLASS__, 'insert_enclosure']);
        }
    }


    /**
    * Update main posts query loop
    *
    * @since 1.7
    */
    public static function update_query($query) {
        // Only for Yandex.Turbo feed
        if($query->is_main_query() && $query->is_feed() && $query->get('feed') === 'turbo') {
            $query->set('posts_per_rss', 50);
            $query->set('post_type', 'post');
            $query->set('post_status', 'publish');
        }
    }


    /**
     * Init Yandex.Zen template feed using custom query
     *
     * @since 1.7
     */
    public static function add_zen_feed() {
        global $post, $wpdb;

        $paged = intval(get_query_var('paged', 1));

        if($paged < 1) {
            $paged = 1;
        }

        $limit = 50;
        $offset = $limit * ($paged - 1);

        $query = "SELECT SQL_CALC_FOUND_ROWS p.*, IFNULL(m2.meta_value, p.post_date_gmt) as zen_date
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} m1 ON (p.ID = m1.post_id AND m1.meta_key = '" . self::$zen_exclude . "')
            LEFT JOIN {$wpdb->postmeta} m2 ON (p.ID = m2.post_id AND m2.meta_key = '" . self::$zen_publish . "')
            WHERE p.post_type = 'post' AND p.post_status = 'publish' AND m1.post_id IS NULL
            GROUP BY p.ID ORDER BY zen_date DESC LIMIT {$offset}, {$limit}";

        $posts = $wpdb->get_results($query, OBJECT);

        require get_template_directory() . '/core/include/feeds/yandex-zen.php';
    }


    /**
     * Init Yandex.Turbo template feed
     *
     * @since 1.7
     */
    public static function add_turbo_feed() {
        require get_template_directory() . '/core/include/feeds/yandex-turbo.php';
    }


    /**
     * Clean content tag
     *
     * @since 1.7
     */
    public static function clean_content($content) {
        $content = preg_replace('~[\r\n]+~', "\n", $content);
        $content = preg_replace('~[ \t]+~', ' ', $content);
        $content = preg_replace('~\s+~', ' ', $content);

        // Remove invalid Char value 3
        $content = preg_replace('~[\x{0003}]+~', ' ', $content);

        // Balances tags of content
        $content = force_balance_tags($content);

        return trim($content);
    }


    /**
     * Remove unused rss tags
     *
     * @since 1.7
     */
    public static function remove_tags($content) {
        $allowed_tags = [
            '<br>','<p>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<a>','<ol>','<li>','<img>','<figcaption>','<figure>','<b>','<strong>','<i>','<em>','<mark>'
        ];

        // Remove unwanted tags
        $content = preg_replace('#<(script|style)[^>]*?>.*?</\\1>#si', '', $content);
        $content = strip_tags($content, implode(',', $allowed_tags));

        // Remove all embeds
        $content = preg_replace('~<figure\s+?class="figure\s+?figure--embed">.+?</figure>~si', '', $content);


        // Remove style and class attributes
        $content = preg_replace('~\s+?style="[^"]+"~', '', $content);
        $content = preg_replace('~\s+?class="[^"]+"~', '', $content);

        return $content;
    }


    /**
     * Remove embeds
     */
    public static function remove_embeds($html, $url, $attr) {
        $html = sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url($url));

        return $html;
    }


    /**
     * Prepend excerpt to post content (except news)
     *
     * @since 1.6
     */
    public static function prepend_lead($content) {
        if(method_exists('Knife_Post_Lead', 'get_lead')) {
            $post_lead = Knife_Post_Lead::get_lead();

            if(strlen($post_lead) > 0) {
                $content = $post_lead . $content;
            }
        }

        return $content;
    }


    /**
     * Update author using coauthors function if the plugin activated
     *
     * @since 1.6
     */
    public static function upgrade_author($author) {
        if(function_exists('coauthors')) {
            $author = coauthors(', ', ', ', null, null, false);
        }

        return $author;
    }


    /**
     * Add post content images
     */
    public static function add_images($content) {
        preg_match_all('~<img.+?src="(.+?)"~is', $content, $images, PREG_PATTERN_ORDER);

        self::$enclosure = [];

        foreach($images[1] as $link) {
            if(!empty($link)) {
                self::$enclosure[] = $link;
            }
        }

        return $content;
    }


    /**
     * Insert post categories
     */
    public static function insert_category() {
        foreach(get_the_category() as $category) {
            printf('<category>%s</category>', esc_html($category->cat_name));
        }
    }


    /**
     * Insert enclosure to content
     *
     * @link https://yandex.ru/support/zen/publishers/rss-modify.html#publication
     */
    public static function insert_enclosure() {
        global $post;

        if(empty(self::$enclosure) && has_post_thumbnail($post->ID)) {
            self::$enclosure[] = get_the_post_thumbnail_url($post->ID, 'outer');
        }

        foreach(self::$enclosure as $image) {
            printf('<enclosure url="%s" type="%s" />', esc_url($image), wp_check_filetype($image)['type']);
        }
    }


    /**
     * Remove unused image attributes
     *
     * @since 1.7
     */
    public static function image_attributes($attr, $attachment, $size) {
        unset($attr['srcset']);
        unset($attr['sizes']);

        return $attr;
    }

}


/**
 * Load current module environment
 */
Knife_Extra_Feeds::load_module();
