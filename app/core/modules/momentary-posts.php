<?php
/**
 * Momentary posts handler
 *
 * Add checkbox to determine posts with momentary content
 *
 * @package knife-theme
 * @since 1.15
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Momentary_Posts {
    /**
     * Post meta to store momentary content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_momentary = '_knife-momentary';


   /**
     * Default post type with momentary content
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to mark momentary content
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox'], 12);

        // Update momentary posts meta
        add_action('save_post', [__CLASS__, 'save_meta']);
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(!in_array($post->post_type, self::$post_type)) {
            return;
        }

        $momentary = get_post_meta($post->ID, self::$meta_momentary, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_momentary),
            __('Сиюминутный контент', 'knife-theme'),
            checked($momentary, 1, false)
        );
    }


    /**
     * Save post meta
     */
    public static function save_meta($post_id) {
        if(isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_momentary])) {
            return delete_post_meta($post_id, self::$meta_momentary);
        }

        return update_post_meta($post_id, self::$meta_momentary, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Momentary_Posts::load_module();
