<?php
/**
 * Short manager
 *
 * Return short links manager with simple stat
 *
 * @package knife-theme
 * @since 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Short_Manager {
    /**
     * Management page slug
     *
     * @access  private
     * @var     string
     */
    private static $page_slug = 'knife-short';


    /**
     * Store tools page base_id screen
     *
     * @access  private
     * @var     string
     */
    private static $screen_base = null;


    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private static $short_db = null;


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action('admin_menu', [__CLASS__, 'add_management_page'], 20);

        // Init second database connection on proper page
        add_action('current_screen', [__CLASS__, 'connect_short_db']);
    }


    /**
     * Add short links management page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            __('Сокращатель ссылок', 'knife-theme'),
            __('Сокращатель ссылок', 'knife-theme'),
            'publish_pages', self::$page_slug,
            [__CLASS__, 'display_management_page']
        );

        // Set tools page base_id screen
        self::$screen_base = $hookname;
    }


    /**
     * Display management page
     */
    public static function display_management_page() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/short-options.php');
    }


    /**
     * Create custom database connection
     */
    public static function connect_short_db() {
        $current_screen = get_current_screen();

        if($current_screen->base === self::$screen_base) {
            if(!defined('WP_SHORT_MANAGER')) {
                define('WP_SHORT_MANAGER', []);
            }

            // Mix with custom module settings
            $settings = wp_parse_args(WP_SHORT_MANAGER, [
                'host' => DB_HOST,
                'name' => DB_NAME,
                'user' => DB_USER,
                'password' => DB_PASSWORD
            ]);

            extract($settings);

            $db = new wpdb($user, $password, $name, $host);

            if(isset($db->error)) {
                wp_die($db->error);
            }

            self::$short_db = $db;
        }
    }
}


/**
 * Load current module environment
 */
Knife_Short_Manager::load_module();
