<?php
/**
 * Extra feeds
 *
 * Add custom feeds templates
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.15
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

        // Don't show link to comments in feed.
        add_filter('comments_open', [__CLASS__, 'disable_comments']);

        // Remove comments feed link from header
        add_filter('feed_links_show_comments_feed', '__return_false');
        add_filter('post_comments_feed_link', '__return_empty_string');

        // Redirect comment feed templates
        add_action('template_redirect', [__CLASS__, 'redirect_comment_feeds']);

        // Remove extra feed links for single
        add_action('wp_head', [__CLASS__, 'remove_head_feeds']);

        if(!defined('KNIFE_TURBO_AD')) {
            define('KNIFE_TURBO_AD', []);
        }

        // Die if php-mb not installed
        if(!function_exists('mb_convert_case')) {
            wp_die(__('Для нормальной работы темы необходимо установить модуль php-mb', 'knife-theme'));
        }
    }


    /**
     * Remove extra feeds from singular
     *
     * @since 1.11
     */
    public static function remove_head_feeds() {
        if(!is_feed() && is_singular()) {
            remove_action('wp_head', 'feed_links_extra', 3);
        }
    }

    /**
     * Redirect comment feed templates
     *
     * @since 1.14
     */
    public static function redirect_comment_feeds() {
        if(!is_admin() && is_feed() && is_singular()) {
            $post_id = get_the_ID();

            if($post_id) {
                wp_safe_redirect(get_permalink($post_id), 301);
            }
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

        delete_post_meta($post_id, self::$zen_publish);

        // Save zen publish date if not empty
        if(!empty($_REQUEST[self::$zen_publish])) {
            $zen_publish = strtotime($_REQUEST[self::$zen_publish]);

            update_post_meta($post_id, self::$zen_publish, date("Y-m-d H:i:s", $zen_publish));
        }

        delete_post_meta($post_id, self::$zen_exclude);

        // Save exclude meta if not empty
        if(!empty($_REQUEST[self::$zen_exclude])) {
            update_post_meta($post_id, self::$zen_exclude, 1);
        }
    }


    /**
     * Update turbo feed
     *
     * @since 1.14
     */
    public static function update_turbo_feed($query) {
        if($query->is_main_query() && $query->is_feed('turbo')) {
            $query->set('category_name', 'news,blunt');
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
     * Hide link to comments for feeds
     *
     * @since 1.15
     */
    public static function disable_comments($open) {
        if(is_feed()) {
            $open = false;
        }

        return $open;
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
            $query->set('post_status', 'publish');
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
     * Get special zen categories for certain post inside loop.
     *
     * @link https://yandex.ru/support/zen/website/rss-modify.html#publication
     * @since 1.15
     */
    private static function get_zen_categories($post_id) {
        $availible = [
            'авто', 'война', 'дизайн', 'дом', 'еда', 'здоровье', 'знаменитости', 'игры',
            'кино', 'культура', 'литература', 'мода', 'музыка', 'наука', 'общество',
            'политика', 'природа', 'происшествия', 'психология', 'путешествия', 'спорт',
            'технологии', 'фотографии', 'хобби', 'экономика', 'юмор'
        ];

        $categories = [];

        if($tags = get_the_tags($post_id)) {
            foreach($tags as $tag) {
                if(in_array($tag->name, $availible, true)) {
                    $categories[] = mb_convert_case($tag->name, MB_CASE_TITLE);
                }
            }
        }

        if(in_category('longreads', $post_id)) {
            $categories[] = 'evergreen';
        }

        return $categories;
    }


    /**
     * Get Yandex.Zen WP_Query args
     */
    private static function get_zen_query() {
        global $wpdb;

        $posts = [];

        // Get 50 published posts
        $results = $wpdb->get_results(self::get_publish_posts(50), OBJECT);

        foreach($results as $result) {
            $posts[$result->id] = $result->post_date;
        }

        // Get zen posts
        $results = $wpdb->get_results(self::get_zen_posts($posts), OBJECT);

        // Update posts with new results
        foreach($results as $result) {
            $posts[$result->post_id] = $result->meta_value;
        }

        // Its ok with sorting by date
        arsort($posts);

        // Exclude posts by meta
        foreach($posts as $id => $publish) {
            if (get_post_meta($id, self::$zen_exclude, true)) {
                unset($posts[$id]);
            }
        }

        return array_slice(array_keys($posts), 0, 20);
    }


    /**
     * Get post date mixed with zen date
     *
     * @since 1.14
     */
    private static function get_zen_date($post_id, $post_date) {
        $zen_date = get_post_meta($post_id, self::$zen_publish, true);

        if (!empty($zen_date)) {
            $post_date = get_gmt_from_date($zen_date);
        }

        return mysql2date('D, d M Y H:i:s +0000', $post_date, false);
    }


    /**
     * Helper method to get query for zen republished posts
     *
     * @since 1.14
     */
    private static function get_zen_posts($posts) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value BETWEEN %s AND NOW()",
            self::$zen_publish, end($posts)
        );

        return $query;
    }


    /**
     * Helper method to get query for published posts
     *
     * @since 1.14
     */
    private static function get_publish_posts($limit) {
        global $wpdb;

        $query = "SELECT id, post_date FROM {$wpdb->posts}
            WHERE post_type = 'post' AND post_status = 'publish'
            ORDER BY post_date DESC LIMIT " . (int) $limit;

        return $query;
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


    /**
     * Prepare content for turbo feed
     *
     * @since 1.15
     */
    private static function prepare_turbo_content($content) {
        $content = preg_replace('~<figure[^>]+>\s+?(<blockquote>.+?</blockquote>)\s+?</figure>~', '$1', $content);

        // Add banners to content.
        $content = self::add_turbo_banners($content);

        return $content;
    }


    /**
     * Add banners to turbo content
     *
     * @link https://yandex.ru/support/adfox-sites/turbo-amp-zen/adfox-turbo.html
     * @since 1.15
     */
    private static function add_turbo_banners($content) {
        $banners = KNIFE_TURBO_AD;

        if(count($banners) < 1) {
            return $content;
        }

        // Find all root tags
        preg_match_all('~<([^\s>]+).+?(?:<\/\1)>~', $content, $matches);

        if(empty($matches[0])) {
            return $content;
        }

        $tags = $matches[0];

        foreach($banners as $id => $position) {
            $banner = sprintf('<figure data-turbo-ad-id="%s"></figure>', $id);

            // If banner position exists in content
            if(!empty($tags[$position])) {
                $tag = $tags[$position];

                // Add new banner before tag
                $content = str_replace($tag, $banner. $tag, $content);
            }
        }

        return $content;
    }
}


/**
 * Load current module environment
 */
Knife_Extra_Feeds::load_module();
