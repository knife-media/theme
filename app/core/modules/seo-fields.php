<?php
/**
 * SEO fields
 *
 * Custom metabox for SEO fields
 *
 * @package knife-theme
 * @since 1.16
 */


if (!defined('WPINC')) {
    die;
}

class Knife_SEO_Fields {
    /**
     * Lead text post meta
     *
     * @access  public
     * @var     string
     */
    public static $meta_seo = '_knife-seo-fields';


    /**
     * Default post type with SEO metabox
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'quiz', 'generator'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add SEO fields metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 12);

        // Save meta
        add_action('save_post', [__CLASS__, 'save_metabox'], 10, 2);

        // Enqueue metabox scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }


    /**
     * Add comment question metabox
     *
     * @since 1.14
     */
    public static function add_metabox() {
        add_meta_box('knife-seo-metabox', __('Информация для поисковых систем', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced', 'high');
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Current screen object
        $screen = get_current_screen();

        if(!in_array($screen->post_type, self::$post_type)) {
            return;
        }

        // Insert admin styles
        wp_enqueue_style('knife-seo-metabox', $include . '/styles/seo-metabox.css', [], $version);
    }


    /**
     * Display SEO fields metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/seo-metabox.php');
    }


    /**
     * Save SEO fields metabox
     */
    public static function save_metabox($post_id, $post) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!isset($_REQUEST[self::$meta_seo])) {
            return;
        }

        $fields = array_filter($_REQUEST[self::$meta_seo]);

        if(empty($fields)) {
            return delete_post_meta($post_id, self::$meta_seo);
        }

        update_post_meta($post_id, self::$meta_seo, $fields);
    }
}


/**
 * Load current module environment
 */
Knife_SEO_Fields::load_module();
