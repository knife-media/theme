<?php
/**
* Terms emoji
*
* Enable terms emojis for default and custom taxonomies
*
* @package knife-theme
* @since 1.5
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Terms_Emoji {
    /**
     * Unique meta to store custom term emoji
     *
     * @since   1.5
     * @access  private
     * @var     string
     */
    private static $meta = '_knife-term-emoji';


    /**
     * Taxes term emoji availible
     *
     * @since   1.5
     * @access  private
     * @var     string
     */
    private static $taxes = ['post_tag', 'label'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add background form fields
        add_action('admin_init', [__CLASS__, 'add_options_fields']);
    }


    /**
     * Add adminside edit form fields
     */
    public static function add_options_fields() {
        foreach(self::$taxes as $tax) {
            add_action("{$tax}_edit_form_fields", [__CLASS__, 'print_options_row'], 10, 2);
            add_action("edited_{$tax}", [__CLASS__, 'save_options_meta']);
        }
    }


    /**
     * Display emoji row
     */
    public static function print_options_row($term, $taxonomy) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/emoji-options.php');
    }


    /**
     * Save emoji meta
     */
    public static function save_options_meta($term_id) {
        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        if(isset($_REQUEST[self::$meta])) {
            $meta = sanitize_text_field($_REQUEST[self::$meta]);

            update_term_meta($term_id, self::$meta, $meta);
        }
    }
}


/**
 * Load current module environment
 */
Knife_Terms_Emoji::load_module();
