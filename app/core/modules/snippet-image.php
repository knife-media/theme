<?php
/**
 * Snippet image
 *
 * Create sharing image for social networks
 *
 * @package knife-theme
 * @since 1.9
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Snippet_Image {
   /**
    * Backward compatibility meta name
    *
    * @access  private
    * @var     string
    */
    private static $post_meta = '_social-image';


    /**
     * Default post type with snippet image metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz', 'select'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-snippet-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        if(current_user_can('edit_posts')) {
            // Add custom distribute metabox
            add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

            // Save metabox
            add_action('save_post', [__CLASS__, 'save_metabox'], 10, 2);

            // Enqueue dashboard widget scripts
//          add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        }
    }


    /**
     * Add snippet image metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-snippet-metabox', __('Изображение соцсети', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');
    }


    /**
     * Display snippet-image metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/snippet-metabox.php');
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id, $post) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Save social-image meta
        if(empty($_REQUEST[self::$post_meta])) {
            return delete_post_meta($post_id, self::$post_meta);
        }

        return update_post_meta($post_id, self::$post_meta, $_REQUEST[self::$post_meta]);
    }

}


/**
 * Load module
 */
Knife_Snippet_Image::load_module();
