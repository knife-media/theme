<?php
/**
 * Post styles
 *
 * Add checkbox to determine posts with custom styles
 *
 * @package knife-theme
 * @since 1.12
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Post_Styles {
    /**
     * Post meta to store adult content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_styles = '_knife-post-styles';


   /**
     * Default post type lead text availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to show custom styles
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox'], 15);

        // Update adult posts meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Enqueue custom post styles
        add_action('wp_enqueue_scripts', [__CLASS__, 'add_post_styles']);
    }


    /**
     * Load custom post styles if exist
     */
    public static function add_post_styles() {
        if(is_singular(self::$post_type)) {
            $object = get_queried_object();

            if(!empty($object->post_name)) {
                // Enqueue custom styles post by slug
                self::enqueue_styles($object->post_name);
            }
        }
    }


    /**
     * Enqueue custom post styles
     */
    private static function enqueue_styles($slug) {
        $version = wp_get_theme()->get('Version');

        if(defined('WP_DEBUG') && true === WP_DEBUG) {
            $version = date('U');
        }

        $styles = "/core/customs/{$slug}.css";

        // Let's add the file if exists
        if(file_exists(get_template_directory() . $styles)) {
            wp_enqueue_style('knife-theme-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);
        }
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(!in_array($post->post_type, self::$post_type)) {
            return;
        }

        $styles = get_post_meta($post->ID, self::$meta_styles, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_styles),
            __('Загружать дополнительные стили', 'knife-theme'),
            checked($styles, 1, false)
        );
    }


    /**
     * Save post meta
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_styles])) {
            return delete_post_meta($post_id, self::$meta_styles);
        }

        return update_post_meta($post_id, self::$meta_styles, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Post_Styles::load_module();
