<?php
/**
 * Promo manager
 *
 * Promo posts classification with custom possibility
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.16
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Promo_Manager {
    /**
     * Default post type with promo checkbox
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'quiz'];


    /**
     * Unique meta to indicate if post promoted
     *
     * @access  public
     * @var     string
     */
    public static $meta_promo = '_knife-promo';


    /**
     * Unique meta to store promo options
     *
     * @access  public
     * @var     string
     */
    public static $meta_options = '_knife-promo-options';


    /**
     * Unique meta to store promo teaser
     *
     * @access  public
     * @var     string
     */
    public static $meta_teaser = '_knife-promo-teaser';


    /**
     * Unique meta to store promo pixel
     *
     * @access  public
     * @var     string
     */
    public static $meta_pixel = '_knife-promo-pixel';


    /**
     * Archive query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'promo';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Create custom promo archive url
        add_action('init', [__CLASS__, 'add_promo_rule']);

        // Set pate links hooks.
        add_action('init', [__CLASS__, 'init_teasers']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_promo_var']);

        // Include archive template for promo posts
        add_filter('template_include', [__CLASS__, 'include_archive']);

        // Show posts only with promo post meta
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Update promo archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_document_title']);

        // Set is-promo class if need
        add_filter('body_class', [__CLASS__, 'set_body_class'], 11);

        // Add snippet image metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Update promo post meta on save post
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Enqueue metabox scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Add promo tag to tag list
        add_filter('term_links-post_tag', [__CLASS__, 'add_promo_tag']);

        // Close comments for promo posts
        add_filter('comments_open',  [__CLASS__, 'disable_comments'], 10, 2);

        // Display teaser state in posts list
        add_filter('display_post_states', [__CLASS__, 'display_teaser_state'], 10, 2);

        // Add pixel image to all widgets
        add_action('the_widget', [__CLASS__, 'display_pixel'], 10, 3);
    }


    /**
     * Set filters to posts permalinks.
     *
     * @since 1.15
     */
    public static function init_teasers() {
        add_filter('post_link',  [__CLASS__, 'update_permalink'], 10, 2);
        add_filter('post_type_link',  [__CLASS__, 'update_permalink'], 10, 2);
        add_filter('preview_post_link', [__CLASS__, 'update_permalink'], 10, 2);

        add_action('template_redirect', [__CLASS__, 'redirect_teaser']);
    }

    /**
     * Display promo pixel in all widgets
     *
     * @since 1.15
     */
    public static function display_pixel($instance, $widget, $args) {
        print_r($args);
        print_r($widget);
    }


    /**
     * Redirect teaser on template_redirect action.
     *
     * @since 1.15
     */
    public static function redirect_teaser() {
        $post_id = get_queried_object_id();

        if(empty($post_id)) {
            return;
        }

        if(!is_singular(self::$post_type)) {
            return;
        }

        $teaser = get_post_meta($post_id, self::$meta_teaser, true);

        if(empty($teaser)) {
            return;
        }

        $link = self::make_absolute_url($teaser);

        wp_redirect($link, 301);
        exit;
    }


    /**
     * Update post permalink for teasers.
     *
     * @since 1.15
     */
    public static function update_permalink($link, $post) {
        if(empty($post->ID)) {
            return $link;
        }

        // Check if in post type.
        if(!in_array($post->post_type, self::$post_type, true)) {
            return $link;
        }

        $teaser = get_post_meta($post->ID, self::$meta_teaser, true);

        if(!empty($teaser)) {
            $link = self::make_absolute_url($teaser);
        }

        return $link;
    }


    /**
     * Try to create absolute url for teaser link
     *
     * @since 1.15
     * @link https://core.trac.wordpress.org/ticket/39827
     */
    public static function make_absolute_url($url) {
        if(strpos($url, '://') === false) {
            $url = site_url($url);
        }

        $path = parse_url($url, PHP_URL_PATH);

        if(null === $path) {
            $url = trailingslashit($url);
        }

        return $url;
    }


    /**
     * Disable comments for promo posts
     *
     * @since 1.14
     */
    public static function disable_comments($open, $post_id) {
        if(get_post_meta($post_id, self::$meta_promo, true)) {
            $open = false;
        }

        return $open;
    }


    /**
     * Display teaser state in posts lists
     */
    public static function display_teaser_state($states, $post) {
        if(get_post_meta($post->ID, self::$meta_teaser, true)) {
            $states[] = __('Пост-ссылка', 'knife-theme');
        }

        return $states;
    }


    /**
     * Create custom promo archive url
     */
    public static function add_promo_rule() {
        add_rewrite_rule(
            sprintf('^%s/?$', self::$query_var),
            sprintf('index.php?%s=1', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/page/([0-9]+)/?$', self::$query_var),
            sprintf('index.php?%s=1&paged=$matches[1]', self::$query_var),
            'top'
        );
    }


    /**
     * Update query_vars for promo archive
     */
    public static function update_query_vars() {
        global $wp_query;

        if(array_key_exists(self::$query_var, $wp_query->query_vars)) {
            $wp_query->is_archive = true;
            $wp_query->is_home = false;
        }
    }


    /**
     * Append promo query tag to availible query vars
     */
    public static function append_promo_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include archive.php template for promo posts
     */
    public static function include_archive($template) {
        if(get_query_var(self::$query_var)) {
            $new_template = locate_template(['archive.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Show only promo posts in custom archive template
     */
    public static function update_query($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        if(get_query_var(self::$query_var)) {
            $query->set('post_type', 'any');
            $query->set('meta_key', self::$meta_promo);
            $query->set('meta_value', 1);
        }
    }


    /**
     * Update post archive caption title
     */
    public static function update_archive_title($title) {
        if(get_query_var(self::$query_var)) {
            $title = sprintf('<h1 class="caption__title">%s</h1>',
                __('Партнерские материалы', 'knife-theme')
            );
        }

        return $title;
    }


    /**
     * Update promo archive document title
     */
    public static function update_document_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Партнерские материалы', 'knife-theme');
        }

        return $title;
    }


    /**
     * Add promo tag to tag list
     *
     * @since 1.9
     */
    public static function add_promo_tag($tags) {
        global $post;

        if(empty($post->ID)) {
            return $tags;
        }

        $is_promo = get_post_meta($post->ID, self::$meta_promo, true);

        if(!$is_promo) {
            return $tags;
        }

        $title = __('реклама', 'knife-theme');

        if(get_the_time('Y-m-d', $post->ID) < '2022-09-01') {
            $title = __('партнерский материал', 'knife-theme');
        }

        $link = sprintf('<a href="%s" rel="tag">%s</a>',
            esc_url(trailingslashit(home_url(self::$query_var))), $title
        );

        array_unshift($tags, $link);

        return $tags;
    }


    /**
     * Add snippet image metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-promo-metabox', __('Настройки промо', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'side');
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        // Current screen object
        $screen = get_current_screen();

        if(!in_array($screen->post_type, self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin styles
        wp_enqueue_style('knife-promo-metabox', $include . '/styles/promo-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-promo-metabox', $include . '/scripts/promo-metabox.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'choose' => __('Выберите спонсорский логотип', 'knife-theme')
        ];

        wp_localize_script('knife-promo-metabox', 'knife_promo_metabox', $options);
    }


    /**
     * Display feed metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/promo-metabox.php');
    }


    /**
     * Save promo post meta
     */
    public static function save_metabox($post_id) {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            $options = $_REQUEST[self::$meta_options];

            // Add # to color if not exists
            if(!empty($options['color'])) {
                $options['color'] = sanitize_hex_color('#' . ltrim($options['color'], '#'));
            }

            update_post_meta($post_id, self::$meta_options, $options);
        }

        // Save promo teaser
        if(empty($_REQUEST[self::$meta_teaser])) {
            delete_post_meta($post_id, self::$meta_teaser);
        }

        if(!empty($_REQUEST[self::$meta_teaser])) {
            $teaser = self::make_absolute_url($_REQUEST[self::$meta_teaser]);

            // Update post name.
            self::replace_teaser_slug($post_id, $teaser);

            update_post_meta($post_id, self::$meta_teaser, $teaser);
        }

        // Save promo pixel
        if(empty($_REQUEST[self::$meta_pixel])) {
            delete_post_meta($post_id, self::$meta_pixel);
        } else {
            update_post_meta($post_id, self::$meta_pixel, sanitize_text_field($_REQUEST[self::$meta_pixel]));
        }

        // Save promo meta
        if(empty($_REQUEST[self::$meta_promo])) {
            delete_post_meta($post_id, self::$meta_promo);
        } else {
            update_post_meta($post_id, self::$meta_promo, 1);
        }
    }


    /**
     * Replace post name for teaser posts to avoid duplicates
     *
     * @since 1.15
     */
    public static function replace_teaser_slug($post_id, $teaser) {
        // Remove action to avoid infinite loop
        remove_action('save_post', [__CLASS__, 'save_metabox']);

        wp_update_post([
            'ID' => $post_id,
            'post_name' => 'teaser-' . dechex(crc32($teaser)),
        ]);

        // Bring back the action
        add_action('save_post', [__CLASS__, 'save_metabox']);
    }


    /**
     * Set is-promo body class
     */
    public static function set_body_class($classes = []) {
        if(is_singular(self::$post_type)) {
            $post_id = get_the_ID();

            if(get_post_meta($post_id, self::$meta_promo, true)) {
                $classes[] = 'is-promo';
            }
        }

        return $classes;
    }

    /**
     * Try to find post id by teaser link.
     */
    public static function find_postid($link) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s LIMIT 1",
            self::$meta_teaser, html_entity_decode($link)
        );

        $post_id = (int) $wpdb->get_var($query);

        return $post_id;
    }
}


/**
 * Load current module environment
 */
Knife_Promo_Manager::load_module();
