<?php
/**
 * Views manager
 *
 * Collect and display page views from Google Analytics
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.14
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Views_Manager {
    /**
     * Management page slug
     *
     * @access  public
     * @var     string
     */
    public static $page_slug = 'knife-views';


    /**
     * Page capability
     *
     * @access  private
     * @var     string
     * @since   1.14
     */
    private static $page_cap = 'promo_manage';


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
    private static $views_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private static $per_page = 'knife_views_per_page';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action('admin_menu', [__CLASS__, 'add_management_page'], 25);

        // Init views manager action before page load
        add_action('current_screen', [__CLASS__, 'init_views_actions']);

        // Save links per page screen option
        add_filter('set-screen-option', [__CLASS__, 'save_screen_options'], 10, 3);

        // Insert new post to views table
        add_action('knife_schedule_views', [__CLASS__, 'start_task']);

        // Schedule insertion to views table
        add_action('transition_post_status', [__CLASS__, 'schedule_insertion'], 10, 3);

        // Define views links settings if still not
        if(!defined('KNIFE_VIEWS')) {
            define('KNIFE_VIEWS', []);
        }
    }


    /**
     * Add views page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            __('Статистика постов', 'knife-theme'),
            __('Статистика постов', 'knife-theme'),
            self::$page_cap, self::$page_slug,
            [__CLASS__, 'display_management_page']
        );

        // Set tools page base_id screen
        self::$screen_base = $hookname;
    }


    /**
     * Views table actions
     */
    public static function init_views_actions() {
        $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

        if(!current_user_can(self::$page_cap)) {
            wp_die(__('Извините, у вас нет доступа к этому инструменту', 'knife-theme'));
        }

        if(!empty($_REQUEST['_wp_http_referer'])) {
            wp_redirect(remove_query_arg(['_wp_http_referer', '_wpnonce'], wp_unslash($_SERVER['REQUEST_URI'])));
            exit;
        }

        // Init second database connection
        self::connect_views_db();

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Add screen options
        add_action('load-' . self::$screen_base, [__CLASS__, 'add_screen_options']);
    }


    /**
     * Start scheduled task
     */
    public static function start_task($post_id) {
        $status = get_post_status($post_id);

        // Init views database commection
        self::connect_views_db();

        // Get views db instance
        $db = self::$views_db;

        if($status === 'trash') {
            return $db->delete('posts', compact('post_id'));
        }

        $slug = wp_make_link_relative(get_permalink($post_id));
        $publish = get_post_field('post_date', $post_id);

        if($status === 'publish') {
            return $db->replace('posts', compact('post_id', 'slug', 'publish'));
        }
    }


    /**
     * Schedule new post to views table
     */
    public static function schedule_insertion($new_status, $old_status, $post) {
        wp_schedule_single_event(time() + 60, 'knife_schedule_views', [$post->ID]);
    }


    /**
     * Save screen options
     * This function should fire earlier than admin_menu hook
     */
    public static function save_screen_options($status, $option, $value) {
        if($option === self::$per_page) {
            return absint($value);
        }

        return $status;
    }


    /**
     * Display management page
     */
    public static function display_management_page() {
        $include = get_template_directory() . '/core/include';

        // Include Views Manager table class
        include_once($include . '/tables/views-manager.php');

        // Get views links table instance
        $table = new Knife_Views_Managers_Table(self::$views_db, self::$per_page);
        $table->prepare_items();

        // Include options template to show table
        include_once($include . '/templates/views-options.php');
    }


    /**
     * Enqueue assets to admin certain screen only
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style('knife-views-options', $include . '/styles/views-options.css', [], $version);
    }


    /**
     * Add tools page screen options
     */
    public static function add_screen_options() {
        add_screen_option('per_page', [
            'option' => self::$per_page
        ]);
    }


    /**
     * Create custom database connection
     */
    private static function connect_views_db() {
        // Mix with default values
        $conf = wp_parse_args(KNIFE_VIEWS, [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD
        ]);

        // Create custom db connection
        $db = new wpdb($conf['user'], $conf['password'], $conf['name'], $conf['host']);
        $db->hide_errors();

        if(isset($db->error)) {
            wp_die($db->error);
        }

        self::$views_db = $db;
    }
}


/**
 * Load current module environment
 */
Knife_Views_Manager::load_module();
