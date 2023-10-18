<?php
/**
 * Analytics manager
 *
 * Collect and display post analytics.
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Analytics_Manager {
    /**
     * Management page slug
     *
     * @access  public
     * @var     string
     */
    public static $page_slug = 'knife-analytics';

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
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private static $per_page = 'knife_analytics_per_page';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action( 'admin_menu', array( __CLASS__, 'add_management_page' ), 25 );

        // Init analytics manager action before page load
        add_action( 'current_screen', array( __CLASS__, 'init_analytics_actions' ) );

        // Save links per page screen option
        add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );

        // Insert new post to analytics table
        add_action( 'knife_schedule_analytics', array( __CLASS__, 'start_task' ) );

        // Schedule insertion to analytics table
        add_action( 'transition_post_status', array( __CLASS__, 'schedule_insertion' ), 10, 3 );

        // Define analytics links settings if still not
        if ( ! defined( 'KNIFE_ANALYTICS' ) ) {
            define( 'KNIFE_ANALYTICS', array() );
        }
    }

    /**
     * Add analytics page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            esc_html__( 'Статистика постов', 'knife-theme' ),
            esc_html__( 'Статистика постов', 'knife-theme' ),
            self::$page_cap,
            self::$page_slug,
            array( __CLASS__, 'display_management_page' )
        );

        // Set tools page base_id screen
        self::$screen_base = $hookname;
    }

    /**
     * Analytics table actions
     */
    public static function init_analytics_actions() {
        $current_screen = get_current_screen();

        if ( $current_screen->base !== self::$screen_base ) {
            return;
        }

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этому инструменту', 'knife-theme' ) );
        }

        if ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
            $unslashed = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

            wp_safe_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), $unslashed ) );
            exit;
        }

        // Add scripts to admin page
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        // Add screen options
        add_action( 'load-' . self::$screen_base, array( __CLASS__, 'add_screen_options' ) );
    }

    /**
     * Start scheduled task
     */
    public static function start_task( $post_id ) {
        $status = get_post_status( $post_id );

        // Init analytics database commection
        $db = self::connect_analytics_db();

        if ( $status === 'trash' ) {
            return $db->delete( 'posts', compact( 'post_id' ) );
        }

        $slug    = wp_make_link_relative( get_permalink( $post_id ) );
        $publish = get_post_field( 'post_date', $post_id );

        if ( $status === 'publish' ) {
            return $db->replace( 'posts', compact( 'post_id', 'slug', 'publish' ) );
        }
    }

    /**
     * Schedule new post to analytics table
     */
    public static function schedule_insertion( $new_status, $old_status, $post ) {
        wp_schedule_single_event( time() + 60, 'knife_schedule_analytics', array( $post->ID ) );
    }

    /**
     * Save screen options
     * This function should fire earlier than admin_menu hook
     */
    public static function save_screen_options( $status, $option, $value ) {
        if ( $option === self::$per_page ) {
            return absint( $value );
        }

        return $status;
    }

    /**
     * Display management page
     */
    public static function display_management_page() {
        $include = get_template_directory() . '/core/include';

        // Include Analytics Manager table class
        include_once $include . '/tables/analytics-posts.php';

        $db = self::connect_analytics_db();

        // Get analytics links table instance
        $table = new Knife_Analytics_Posts_Table( $db, self::$per_page );
        $table->prepare_items();

        // Include options template to show table
        include_once $include . '/templates/analytics-posts.php';
    }

    /**
     * Enqueue assets to admin certain screen only
     */
    public static function enqueue_assets() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style( 'knife-analytics-options', $include . '/styles/analytics-options.css', array(), $version );
    }

    /**
     * Add tools page screen options
     */
    public static function add_screen_options() {
        add_screen_option(
            'per_page',
            array(
                'option' => self::$per_page,
            )
        );
    }

    /**
     * Create custom database connection
     */
    private static function connect_analytics_db() {
        // Mix with default values
        $conf = wp_parse_args(
            KNIFE_ANALYTICS,
            array(
                'host'     => DB_HOST,
                'name'     => DB_NAME,
                'user'     => DB_USER,
                'password' => DB_PASSWORD,
            )
        );

        // Create custom db connection
        $db = new wpdb( $conf['user'], $conf['password'], $conf['name'], $conf['host'] );

        return $db;
    }
}

/**
 * Load current module environment
 */
Knife_Analytics_Manager::load_module();
