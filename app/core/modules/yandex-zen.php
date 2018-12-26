<?php
/**
* Yandex.Zen feed template
*
* Add custom Yandex.Zen feed template
*
* @package knife-theme
* @since 1.2
* @version 1.7
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Yandex_Zen {

    /**
     * Exclude from feed post meta
     */
    private static $meta_exclude = '_knife-zen-exclude';

    /**
     * Republish post meta
     */
    private static $meta_publish = '_knife-zen-publish';

    /**
     * Feed slug and template name
     */
    private static $slug = 'zen';

    /**
     * Content allowed tags
     */
    private static $tags = ['<br>','<p>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<ol>','<li>','<img>','<figcaption>','<figure>','<b>','<strong>','<i>','<em>', '<mark>'];

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
    private static $nonce = 'knife-zen-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_action('init', [__CLASS__, 'init_feeds']);
        add_action('wp', [__CLASS__, 'add_hooks']);

        // Update zen feed meta on save post
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
        wp_enqueue_script('knife-zen-metabox', $include . '/scripts/zen-metabox.js', ['jquery'], $version);
    }


    /**
     * Add zen publish metabox
     *
     * @since 1.6
     */
    public static function add_metabox() {
        add_meta_box('knife-zen-metabox', __('Яндекс.Дзен', 'knife-theme'), [__CLASS__, 'display_metabox'], ['post'], 'side', 'default');
    }


    /**
     * Display zen metabox
     *
     * @since 1.6
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/zen-metabox.php');
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
        if(!empty($_REQUEST[self::$meta_publish])) {
            update_post_meta($post_id, self::$meta_publish, sanitize_text_field($_REQUEST[self::$meta_publish]));
        } else {
            delete_post_meta($post_id, self::$meta_publish);
        }

        // Save exclude meta if not empty
        if(!empty($_REQUEST[self::$meta_exclude])) {
            update_post_meta($post_id, self::$meta_exclude, 1);
        } else {
            delete_post_meta($post_id, self::$meta_exclude);
        }
    }


    /**
     * Update custom feed template
     */
    public static function add_hooks() {
        if(is_feed(self::$slug)) {
            // Remove post content unwanted tags
            add_filter('the_content_feed', [__CLASS__, 'clear_content'], 10);

            // Add excerpt except news
            add_filter('the_content_feed', [__CLASS__, 'prepend_excerpt'], 12);

            // Upgrade post author with coauthors plugin
            add_filter('the_author', [__CLASS__, 'upgrade_author']);

            // Add post category and enclosure after content
            add_action('rss2_item', [__CLASS__, 'insert_category']);
            add_action('rss2_item', [__CLASS__, 'insert_enclosure']);

            // update rss2 head with atom link
            add_action('rss2_head', [__CLASS__, 'update_head']);
        }
    }


    /**
     * Register new feed endpoint
     */
    public static function init_feeds() {
        add_feed(self::$slug, [__CLASS__, 'add_feed']);
    }


    /**
     * Init template feed using custom query
     *
     * @version 1.6
     */
    public static function add_feed() {
        global $post, $wpdb;

        $query = "SELECT SQL_CALC_FOUND_ROWS p.*, IFNULL(m2.meta_value, p.post_date_gmt) as zen_date
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} m1 ON (p.ID = m1.post_id AND m1.meta_key = '" . self::$meta_exclude . "')
            LEFT JOIN {$wpdb->postmeta} m2 ON (p.ID = m2.post_id AND m2.meta_key = '" . self::$meta_publish . "')
            WHERE p.post_type = 'post' AND p.post_status = 'publish' AND m1.post_id IS NULL
            GROUP BY p.ID ORDER BY zen_date DESC LIMIT 0, 50";

        $posts = $wpdb->get_results($query, OBJECT);

        require get_template_directory() . '/core/include/feeds/' . self::$slug . '.php';
    }


    /**
     * Add atom self link
     */
    public static function update_head() {
        printf('<atom:link href="%s" rel="self" type="application/rss+xml" />', get_feed_link(self::$slug));
    }


    /**
     * Upgrade content tag
     */
    public static function clear_content($content) {
        $content = self::add_images($content);

        $content = self::strip_tags($content, self::$tags);
        $content = self::clear_xml($content);


        return $content;
    }


    /**
     * Prepend excerpt to post content (except news)
     *
     * @since 1.6
     */
    public static function prepend_excerpt($content) {
        if(!in_category('news') && has_excerpt()) {
            $content = apply_filters('the_excerpt', get_the_excerpt()) . $content;
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
     * Strip tags, scripts and styles
     */
    private static function strip_tags($string, $allowable_tags = null) {
        $string = preg_replace('#<(script|style)[^>]*?>.*?</\\1>#si', '', $string);
        $string = strip_tags($string, implode(',', $allowable_tags));

        return $string;
    }


    /**
     * Remove unwanted content
     */
    private static function clear_xml($string) {
        $string = preg_replace('/[\r\n]+/', "\n", $string);
        $string = preg_replace('/[ \t]+/', ' ', $string);

        $string = preg_replace('~<figure\s+?class="figure\s+?figure--embed">.+?</figure>~si', '', $string);

        $string = preg_replace('/ style="[^"]+"/', '', $string);
        $string = preg_replace('/ srcset="[^"]+"/', '', $string);
        $string = preg_replace('/ sizes="[^"]+"/', '', $string);
        $string = preg_replace('/ class="[^"]+"/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = preg_replace('/<[^\/>]*><\/[^>]*>/', '', $string);

        $string = str_replace('&hellip;', '...', $string);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = str_replace('> <', '><', $string);
        $string = str_replace(PHP_EOL, '', $string);

        $string = force_balance_tags($string);

        return trim($string);
    }
}


/**
 * Load current module environment
 */
Knife_Yandex_Zen::load_module();
