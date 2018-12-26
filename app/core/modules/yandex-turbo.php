<?php
/**
* Yandex.Turbo feed template
*
* Add custom Yandex.Turbo feed template
*
* @package knife-theme
* @since 1.7
* @link https://yandex.ru/support/webmaster/turbo/feed.html
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Yandex_Turbo {
    /**
     * Feed slug and template name
     */
    private static $slug = 'turbo';


    /**
     * Content allowed tags
     */
    private static $tags = ['<br>','<p>', '<h1>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<ol>','<li>','<img>', 'iframe', '<figcaption>','<figure>','<b>','<strong>','<i>','<em>', '<mark>', '<sup>', '<sub>'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        add_action('init', [__CLASS__, 'init_feed']);
        add_action('wp', [__CLASS__, 'add_hooks']);

        // Alter feed main query loop
        add_action('pre_get_posts', [__CLASS__, 'update_query']);
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

            // Remove unused images attributes
            add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'image_attributes'], 10, 3);
        }
    }


    /**
     * Register new feed endpoint
     */
    public static function init_feed() {
        add_feed(self::$slug, [__CLASS__, 'add_feed']);
    }


    /**
     * Init template feed using custom query
     */
    public static function add_feed() {
        require get_template_directory() . '/core/include/feeds/' . self::$slug . '.php';
    }


   /**
    * Update posts query
    */
    public static function update_query($query) {
        if($query->is_main_query() && $query->is_feed() && self::$slug === $query->get('feed')) {
            $query->set('posts_per_rss', 50);
            $query->set('post_type', 'post');
            $query->set('post_status', 'publish');
        }
    }


    /**
     * Upgrade content tag
     */
    public static function clear_content($content) {
        $content = strip_tags($content, implode(',', self::$tags));

        // Remove script and styles insertions
        $cotnent = preg_replace('~<(script|style)[^>]*?>.*?</\\1>~si', '', $content);

        // Remove all class attributes
        $content = preg_replace('~\s+class="[^"]+"~is', '', $content);

        // Remove tabs and brak lines
        $content = preg_replace('/[\r\n]+/', "\n", $content);
        $content = preg_replace('/[\t]+/', '', $content);

        $content = force_balance_tags($content);

        return trim($content);
    }


    /**
     * Prepend excerpt to post content (except news)
     */
    public static function prepend_excerpt($content) {
        if(!in_category('news') && has_excerpt()) {
            $content = apply_filters('the_excerpt', get_the_excerpt()) . $content;
        }

        return $content;
    }


    /**
     * Update author using coauthors function if the plugin activated
     */
    public static function upgrade_author($author) {
        if(function_exists('coauthors')) {
            $author = coauthors(', ', ', ', null, null, false);
        }

        return $author;
    }


    /**
     * Remove unused image attributes
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
Knife_Yandex_Turbo::load_module();
