<?php
/**
* Extra feeds
*
* Add custom feeds templates
*
* @package knife-theme
* @since 1.7
* @link https://yandex.ru/support/news/feed.html
* @link https://yandex.ru/support/zen/website/rss-modify.html
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Extra_Feeds {
    /**
     * Exclude post from Yandex.Zen meta
     *
     * @access  private static
     * @var     string
     */
    private static $zen_exclude = '_knife-zen-exclude';


    /**
     * Republish Yandex.Zen meta
     *
     * @access  private static
     * @var     string
     */
    private static $zen_publish = '_knife-zen-publish';


    /**
     * Checkbox save nonce
     *
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-feed-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Init custom feeds
        add_action('init', [__CLASS__, 'add_feeds']);

        // Alter feed main query loop
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Upgrade post author with coauthors plugin
        add_filter('the_author', [__CLASS__, 'upgrade_author']);

        // Remove unused images attributes
        add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'image_attributes'], 10, 3);

        // Update embed html for feeds
        add_filter('embed_oembed_html', [__CLASS__, 'update_embeds'], 15, 3);

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
     */
    public static function add_metabox() {
        add_meta_box('knife-feed-metabox', __('Настройки RSS', 'knife-theme'), [__CLASS__, 'display_metabox'], ['post'], 'side', 'default');
    }


    /**
     * Display feed metabox
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
    public static function add_feeds() {
        $feeds = ['zen', 'turbo', 'yandex-news'];

        foreach($feeds as $feed) {
            add_feed($feed, [__CLASS__, 'load_template']);
        }
    }


    /**
     * Include feed template if exists
     */
    public static function load_template() {
        $feed = get_query_var('feed');

        // Get feed path
        $path = get_template_directory() . '/core/include/feeds/' . $feed . '.php';

        if(file_exists($path)) {
            require $path;
        }
    }


    /**
    * Update main posts query loop
    */
    public static function update_query($query) {
        if($query->is_main_query() && $query->is_feed()) {
            $query->set('posts_per_rss', 50);
        }
    }


    /**
     * Remove unused image attributes
     */
    public static function image_attributes($attr, $attachment, $size) {
        if(is_feed()) {
            unset($attr['srcset']);
            unset($attr['sizes']);
        }

        return $attr;
    }


    /**
     * Update author using coauthors function if the plugin activated
     */
    public static function upgrade_author($author) {
        if(is_feed() && function_exists('coauthors')) {
            $author = coauthors(', ', ', ', null, null, false);
        }

        return $author;
    }


    /**
     * Update embeds for feeds
     */
    public static function update_embeds($html, $url, $attr) {
        if(is_feed()) {
            $html = sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url($url));
        }

        return $html;
    }


    /**
     * Clean content tag
     */
    private static function clean_content($content) {
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
     */
    private static function remove_tags($content) {
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
     * Add post content images
     */
    private static function get_images($content, $post_id) {
        preg_match_all('~<img.+?src="(.+?)"~is', $content, $images, PREG_PATTERN_ORDER);

        $enclosure = [];

        foreach($images[1] as $link) {
            if(!empty($link)) {
                $enclosure[] = $link;
            }
        }

        // Add thumbnail as enclosure if post does not contain images
        if(empty($enclosure) && has_post_thumbnail($post_id)) {
            $enclosure[] = get_the_post_thumbnail_url($post_id, 'outer');
        }

        return $enclosure;
    }
}


/**
 * Load current module environment
 */
Knife_Extra_Feeds::load_module();
