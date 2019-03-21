<?php
/**
 * Promo manager
 *
 * Promo posts classification with custom possibility
 *
 * @package knife-theme
 * @since 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Promo_Manager {
    /**
     * Default post type with promo checkbox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Unique meta to indicate if post promoted
     *
     * @access  private
     * @var     string
     */
    private static $meta_promo = '_knife-promo';


    /**
     * Checkbox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-club-form-nonce';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Set is-promo class if need
        add_filter('body_class', [__CLASS__, 'set_body_class'], 11, 1);

        // Promo checkbox
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox']);

        // Update promo post meta on save post
        add_action('save_post', [__CLASS__, 'save_metabox']);
    }


    /**
     * Print checkbox in post publish action section
     */
    public static function print_checkbox() {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        $promo = get_post_meta($post_id, self::$meta_promo, true);

        printf(
            '<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_promo),
            __('Партнерский материал', 'knife-theme'),
            checked($promo, 1, false)
        );


        wp_nonce_field('checkbox', self::$metabox_nonce);
    }


    /**
     * Save promo post meta
     */
    public static function save_metabox($post_id) {
        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'checkbox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_promo])) {
            return delete_post_meta($post_id, self::$meta_promo);
        }

        return update_post_meta($post_id, self::$meta_promo, 1);
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
}


/**
 * Load current module environment
 */
Knife_Promo_Manager::load_module();
