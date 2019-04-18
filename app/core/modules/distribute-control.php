<?php
/**
 * Distribute control
 *
 * Schedule posts publishing to social networks
 *
 * @package knife-theme
 * @since 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Distribute_Control {
    /**
     * Default post type with social distribute metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Unique meta to store post distribute options
     *
     * @access  private
     * @var     string
     */
    private static $post_meta = '_knife-distribute';


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-distribute-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom distribute metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);
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

        if(!in_array($screen->post_type,  self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style('knife-distribute-metabox', $include . '/styles/distribute-metabox.css', [], $version);
    }


    /**
     * Add custom distribute metabox for editors and admins
     */
    public static function add_metabox() {
        if(current_user_can('publish_pages')) {
            add_meta_box('knife-distribute-metabox', __('Настройки кросспостинга'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');

            // Enqueue post metabox scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        }
    }


    /**
     * Display distribute metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/distribute-metabox.php');
    }
}


/**
 * Load module
 */
Knife_Distribute_Control::load_module();
