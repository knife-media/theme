<?php
/**
 * Extra feeds
 *
 * Add custom feeds templates
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.13
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
     * @access  private
     * @var     string
     */
    private static $zen_exclude = '_knife-zen-exclude';


    /**
     * Republish Yandex.Zen meta
     *
     * @access  private
     * @var     string
     */
    private static $zen_publish = '_knife-zen-publish';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Init custom feeds
        add_action('init', [__CLASS__, 'add_feeds']);

        // Alter feed main query loop
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Show only news in yandex-news feed
        add_action('pre_get_posts', [__CLASS__, 'update_yandex_news_feed']);

        // Show only news in turbo feed
        add_action('pre_get_posts', [__CLASS__, 'update_turbo_feed']);

        // Remove unused images attributes
        add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'image_attributes'], 10, 3);

        // Update feed meta on save post
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Post metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 9);

        // Admin side assets
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // Remove comments feed link from header
        add_filter('feed_links_show_comments_feed', '__return_false');
        add_filter('post_comments_feed_link', '__return_false');

        // Remove comment feeds
        add_action('do_feed_rss2', [__CLASS__, 'remove_comment_feeds'], 9);
        add_action('do_feed_atom', [__CLASS__, 'remove_comment_feeds'], 9);

        // Remove extra feed links for single
        add_action('wp', [__CLASS__, 'remove_head_feeds']);
    }


    /**
     * Remove extra feeds from singular
     *
     * @since 1.11
     */
    public static function remove_head_feeds() {
        if(is_singular()) {
            remove_action('wp_head', 'feed_links_extra', 3);
        }
    }


    /**
     * Remove comment feeds
     *
     * @since 1.11
     */
    public static function remove_comment_feeds($for_comments) {
        if($for_comments) {
            remove_action('do_feed_rss2', 'do_feed_rss2');
            remove_action('do_feed_atom', 'do_feed_atom');
        }
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        if(get_post_type($post->ID) !== 'post') {
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
    public static function save_metabox($post_id) {
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
     * Update turbo feed
     *
     * @since 1.14
     */
    public static function update_turbo_feed($query) {
        if($query->is_main_query() && $query->is_feed('turbo')) {
            $query->set('category_name', 'news');
        }
    }


    /**
     * Update yandex.news feed
     *
     * @since 1.11
     */
    public static function update_yandex_news_feed($query) {
        if($query->is_main_query() && $query->is_feed('yandex-news')) {
            $query->set('category_name', 'news');
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

        // Feeds templates path
        $path = get_template_directory() . '/core/include/feeds/';

        // Include if exists
        include_once($path . $feed . '.php');
    }


    /**
    * Update main posts query loop
    */
    public static function update_query($query) {
        if($query->is_main_query() && $query->is_feed()) {
            $query->set('posts_per_rss', 25);
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
     * Replace embed html with links
     */
    public static function replace_embeds($html, $url, $attr) {
        if(is_feed()) {
            $html = sprintf('<a href="%1$s" target="_blank">%1$s</a>', esc_url($url));
        }

        return $html;
    }


    /**
     * Custom filtered post content
     */
    private static function get_filtered_content() {
        // Replace embed html with links
        add_filter('embed_oembed_html', [__CLASS__, 'replace_embeds'], 15, 3);

        $content = get_the_content_feed();

        // Remove filter immediately after content get
        remove_filter('embed_oembed_html', [__CLASS__, 'replace_embeds'], 15, 3);

        return $content;
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
            '<br>','<p>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<a>','<ol>','<li>','<img>','<blockquote>','<figcaption>','<figure>','<b>','<strong>','<i>','<em>','<mark>'
        ];

        // Remove unwanted tags
        $content = preg_replace('#<(script|style)[^>]*?>.*?</\\1>#si', '', $content);
        $content = strip_tags($content, implode(',', $allowed_tags));

        // Remove all embeds
        $content = preg_replace('~<figure\s+?class="figure\s+?figure--embed">.+?</figure>~si', '', $content);


        // Remove style and class attributes
        $content = preg_replace('~\s+?style="[^"]+"~', '', $content);
        $content = preg_replace('~\s+?class="[^"]+"~', '', $content);

        // Clean markup
        $content = str_replace('<p></p>', '', $content);

        return $content;
    }


    /**
     * Add post content images
     */
    private static function get_images($content, $post_id) {
        $enclosure = [];

        // Add thumbnail as enclosure of exists
        if(has_post_thumbnail($post_id)) {
            $enclosure[] = get_the_post_thumbnail_url($post_id, 'outer');
        }

        preg_match_all('~<img.+?src="(.+?)"~is', $content, $images, PREG_PATTERN_ORDER);

        foreach($images[1] as $link) {
            if(!empty($link)) {
                $enclosure[] = $link;
            }
        }

        return $enclosure;
    }
}


/**
 * Load current module environment
 */
Knife_Extra_Feeds::load_module();
