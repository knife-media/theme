<?php
/**
 * Social publisher
 *
 * Schedule posts sharing
 *
 * @package knife-theme
 * @since 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Social_Publisher {
    /**
     * Default post type with social publisher metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Unique meta to store post publisher options
     *
     * @access  private
     * @var     string
     */
    private static $post_meta = '_knife-publisher';


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-publisher-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom publisher metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);
    }


    /**
     * Add custom publisher metabox for editors and admins
     */
    public static function add_metabox() {
        if(current_user_can('publish_pages')) {
            add_meta_box('knife-publisher-metabox', __('Настройки кросспостинга'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');

            // Enqueue post metabox scripts
//            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_metabox_assets']);
        }
    }


    /**
     * Display publisher metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        echo '123';
//        include_once($include . '/templates/background-metabox.php');
    }
}


/**
 * Load module
 */
Knife_Social_Publisher::load_module();
