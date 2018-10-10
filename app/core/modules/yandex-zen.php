<?php
/**
* Yandex.Zen feed template
*
* Add custom Yandex.Zen feed template
*
* @package knife-theme
* @since 1.2
* @version 1.5
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Yandex_Zen {

    /**
    * Exclude post item meta
    */
    private static $meta = '_knife-yandex-zen';

    /**
    * Feed slug and template name
    */
    private static $slug = 'zen';

    /**
    * Content allowed tags
    */
    private static $tags = ['<br>','<p>','<h2>','<h3>','<h4>','<h5>','<h6>','<ul>','<ol>','<li>','<img>','<figcaption>','<figure>','<b>','<strong>','<i>','<em>'];

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
    private static $nonce = 'knife-yandex-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_action('init', [__CLASS__, 'init_feeds']);
        add_action('wp', [__CLASS__, 'add_hooks']);

        // Alter feed main query loop
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update zen feed meta on save post
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Feature post meta
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox']);
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox() {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'post') {
            return;
        }

        $exclude = get_post_meta($post_id, self::$meta, true);

        printf(
            '<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta),
            __('Исключить запись из Яндекс.Дзен', 'knife-theme'),
            checked($exclude, 1, false)
        );

        wp_nonce_field('checkbox', self::$nonce);
    }


    /**
     * Save feed post meta
     */
    public static function save_meta($post_id) {
        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'checkbox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta])) {
            return delete_post_meta($post_id, self::$meta);
        }

        return update_post_meta($post_id, self::$meta, 1);
    }


    /**
    * Update custom feed template
    */
    public static function add_hooks() {
        if(!is_feed(self::$slug)) {
            return;
        }

        // remove post content unwanted tags
        add_filter('the_content_feed', [__CLASS__, 'clear_content']);

        // add post category and enclosure after content
        add_action('rss2_item', [__CLASS__, 'insert_category']);
        add_action('rss2_item', [__CLASS__, 'insert_enclosure']);

        // update rss2 head with atom link
        add_action('rss2_head', [__CLASS__, 'update_head']);
    }


    /**
    * Register new feed endpoint
    */
    public static function init_feeds() {
        add_feed(self::$slug, [__CLASS__, 'add_feed']);
    }


    /**
    * Update posts query
    */
    public static function update_query($query) {
        if($query->is_main_query() && $query->is_feed() && self::$slug === $query->get('feed')) {
            $query->set('posts_per_rss', 50);
            $query->set('post_type', 'post');
            $query->set('post_status', 'publish');

            $query->set('meta_query', [[
                'key' => self::$meta,
                'compare' => 'NOT EXISTS'
            ]]);
        }
    }


    /**
    * Init template feed
    */
    public static function add_feed() {
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
    * Add post thumbnail to enclosure list
    */
    public static function add_thumbnail() {
        global $post;

        if(empty(self::$enclosure) && has_post_thumbnail($post->ID)) {
            self::$enclosure[] = get_the_post_thumbnail_url($post->ID, 'outer');
        }
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
        self::add_thumbnail();

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
