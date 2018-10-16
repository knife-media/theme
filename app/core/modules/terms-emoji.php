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

        // Add custom column to term table screen
        add_action('admin_init', [__CLASS__, 'add_terms_column']);
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
     * Add custom terms column
     */
    public static function add_terms_column() {
        foreach(self::$taxes as $tax) {
            add_action("manage_edit-{$tax}_columns", [__CLASS__, 'add_column_title']);
            add_action("manage_{$tax}_custom_column", [__CLASS__, 'add_column_emoji'], 10, 3);
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


    /**
     * Add emoji column title
     */
    public static function add_column_title($columns) {
        $title = [
            'term-emoji' => __('Эмодзи', 'knife-theme')
        ];

        return array_slice($columns, 0, 3, true) + $title + array_slice($columns, 3, NULL, true);
    }


    /**
     * Add emoji column content
     */
    public static function add_column_emoji($content, $column, $term_id) {
        if($column === 'term-emoji') {
            $content = get_term_meta($term_id, self::$meta, true);
        }

        return $content;
    }
}


/**
 * Load current module environment
 */
Knife_Terms_Emoji::load_module();
