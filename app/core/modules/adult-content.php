<?php
/**
 * Adult content
 *
 * Add checkbox to determine posts with adult content
 *
 * @package knife-theme
 * @since 1.11
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Adult_Content {
    /**
     * Post meta to store adult content option
     *
     * @access  private
     * @var     string
     */
    private static $post_meta = '_knife_adult_content';


   /**
     * Default post type lead text availible
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'select', 'generator', 'quiz'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to mark adult content
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox'], 9);

        // Update adult posts meta
        add_action('save_post', [__CLASS__, 'save_meta']);
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox() {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        $adult = get_post_meta($post_id, self::$post_meta, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$post_meta),
            __('Содержимое для взрослых', 'knife-theme'),
            checked($adult, 1, false)
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

        if(empty($_REQUEST[self::$post_meta])) {
            return delete_post_meta($post_id, self::$post_meta);
        }

        return update_post_meta($post_id, self::$post_meta, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Adult_Content::load_module();
