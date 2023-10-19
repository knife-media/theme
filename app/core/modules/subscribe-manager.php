<?php
/**
 * Subscribe manager
 *
 * Shows subscribe with simple statistics
 *
 * @package knife-theme
 * @since 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Subscribe_Manager {
    /**
     * Management page slug
     *
     * @access  public
     * @var     string
     */
    public static $page_slug = 'knife-subscribe';

    /**
     * Page capability
     *
     * @access  private
     * @var     string
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
    private static $per_page = 'knife_subscribe_per_page';

    /**
     * Subscribe database instance
     *
     * @access  private
     * @var     object
     */
    private static $subscribe_db = null;

    /**
     * Current page admin url
     *
     * @access  private
     * @var     string
     */
    private static $admin_url = '';

    /**
     * Pass table prepared value from request id
     *
     * @access  private
     * @var     string
     */
    private static $prepared = null;

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action( 'admin_menu', array( __CLASS__, 'add_management_page' ), 20 );

        // Init short links action before page load
        add_action( 'current_screen', array( __CLASS__, 'init_actions' ) );

        // Admin-post action save letter
        add_action( 'admin_post_' . self::$page_slug . '-submit', array( __CLASS__, 'submit_letter' ) );

        // Save links per page screen option
        add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );

        self::$admin_url = admin_url( '/tools.php?page=' . self::$page_slug );

        // Define short links settings if still not
        if ( ! defined( 'KNIFE_SUBSCRIBE' ) ) {
            define( 'KNIFE_SUBSCRIBE', array() );
        }

        // Die if php-mb not installed
        if ( ! function_exists( 'mb_strlen' ) ) {
            wp_die( esc_html__( 'Для нормальной работы темы необходимо установить модуль php-mb', 'knife-theme' ) );
        }
    }

    /**
     * Add short links management page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            esc_html__( 'Менеджер рассылок', 'knife-theme' ),
            esc_html__( 'Менеджер рассылок', 'knife-theme' ),
            self::$page_cap,
            self::$page_slug,
            array( __CLASS__, 'display_management_page' )
        );

        // Set tools page base_id screen
        self::$screen_base = $hookname;
    }

    /**
     * Short links actions
     */
    public static function init_actions() {
        $current_screen = get_current_screen();

        if ( $current_screen->base !== self::$screen_base ) {
            return;
        }

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этой странице', 'knife-theme' ) );
        }

        self::$subscribe_db = self::connect_subscribe_db();

        self::handle_tab_actions();

        // Add scripts to admin page
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
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
        $message = isset( $_REQUEST['message'] ) ? absint( $_REQUEST['message'] ) : 0;

        switch ( $message ) {
            case 1:
                add_settings_error(
                    'knife-subscribe-actions',
                    'append',
                    esc_html__( 'Рассылка успешно сохранена', 'knife-theme' ),
                    'updated'
                );
                break;
            case 2:
                add_settings_error(
                    'knife-subscribe-actions',
                    'error',
                    esc_html__( 'Не удалось сохранить рассылку', 'knife-theme' )
                );
                break;
            case 3:
                add_settings_error(
                    'knife-subscribe-actions',
                    'deleted',
                    esc_html__( 'Выбранные рассылки успешно удалены', 'knife-theme' ),
                    'updated'
                );
                break;

            case 4:
                add_settings_error(
                    'knife-subscribe-actions',
                    'scheduled',
                    esc_html__( 'Рассылка успешно запланирована', 'knife-theme' ),
                    'updated'
                );
                break;

            case 5:
                add_settings_error(
                    'knife-subscribe-actions',
                    'updated',
                    esc_html__( 'Статус пользователей успешно изменен', 'knife-theme' ),
                    'updated'
                );
                break;

            case 6:
                add_settings_error(
                    'knife-subscribe-actions',
                    'error',
                    esc_html__( 'Не удалось запланировать рассылку. Проверьте дату и время', 'knife-theme' )
                );
                break;

            case 7:
                add_settings_error(
                    'knife-subscribe-actions',
                    'canceled',
                    esc_html__( 'Рассылка успешно отменена. Запланируйте ее заново', 'knife-theme' ),
                    'updated'
                );
                break;
        }

        if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] === 'users' ) {
            return self::show_users_tab();
        }

        if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] === 'stats' ) {
            return self::show_clicks_tab();
        }

        self::show_default_tab();
    }

    /**
     * Enqueue assets to admin certain screen only
     */
    public static function enqueue_assets() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style( 'knife-subscribe-options', $include . '/styles/subscribe-options.css', array(), $version );
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
    private static function connect_subscribe_db() {
        // Mix with default values
        $conf = wp_parse_args(
            KNIFE_SUBSCRIBE,
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

    /**
     * Handle letter submission
     */
    public static function submit_letter() {
        check_admin_referer( 'knife-subscribe-submit' );

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этой странице', 'knife-theme' ) );
        }

        self::$subscribe_db = self::connect_subscribe_db();

        if ( isset( $_POST['schedule'], $_REQUEST['id'] ) ) {
            return self::schedule_letter( absint( $_REQUEST['id'] ) );
        }

        if ( empty( $_POST['title'] || empty( $_POST['content'] ) ) ) {
            return self::redirect_with_message( 3 );
        }

        $data = array(
            'title'   => sanitize_text_field( wp_unslash( $_POST['title'] ) ),
            'content' => wp_kses_post( wp_unslash( $_POST['content'] ) ),
            'updated' => current_time( 'mysql' ),
            'status'  => 'draft',
        );

        if ( empty( $_REQUEST['id'] ) ) {
            $result = self::$subscribe_db->insert( 'letters', $data );

            if ( ! $result ) {
                return self::redirect_with_message( 2 );
            }

            return self::redirect_with_message( 1, array( 'id' => self::$subscribe_db->insert_id ) );
        }

        $args = array( 'id' => absint( $_REQUEST['id'] ) );

        // Try to update current letter id
        self::$subscribe_db->update( 'letters', $data, $args );

        return self::redirect_with_message( 1, $args );
    }

    /**
     * Show users tab on management page
     */
    private static function show_users_tab() {
        $include = get_template_directory() . '/core/include';

        include_once $include . '/tables/subscribe-users.php';

        // Get subscribe links table instance
        $table = new Knife_Subscribe_Users_Table( self::$subscribe_db );
        $table->prepare_items();

        include_once $include . '/templates/subscribe-users.php';
    }

    /**
     * Show users tab on management page
     */
    private static function show_clicks_tab() {
        $include = get_template_directory() . '/core/include';

        $prepared  = self::$prepared;
        $analytics = self::get_analytics_data( $prepared['id'] );

        include_once $include . '/templates/subscribe-stats.php';
    }

    /**
    * Show default tab on management page
     */
    private static function show_default_tab() {
        $include = get_template_directory() . '/core/include';

        include_once $include . '/tables/subscribe-letters.php';

        $table = new Knife_Subscribe_Letters_Table( self::$subscribe_db, self::$per_page, self::$admin_url );
        $table->prepare_items();

        $defaults = array(
            'title'   => '',
            'content' => '',
            'status'  => 'draft',
        );

        $prepared = self::$prepared;

        if ( $prepared === null ) {
            $prepared = $defaults;
        }

        $summary = self::get_default_summary();

        include_once $include . '/templates/subscribe-options.php';
    }

    /**
     * Choose and handle tab actions according request param
     */
    private static function handle_tab_actions() {
        if ( empty( $_REQUEST['tab'] ) ) {
            // Screen options only for default tab
            add_action( 'load-' . self::$screen_base, array( __CLASS__, 'add_screen_options' ) );

            return self::prepare_default_actions();
        }

        $tab = sanitize_key( $_REQUEST['tab'] );

        if ( $tab === 'stats' ) {
            return self::prepare_clicks_actions();
        }

        if ( $tab === 'users' ) {
            return self::prepare_users_actions();
        }
    }

    /**
     * Process initial actions and prepare data on default subscribe tab
     */
    private static function prepare_default_actions() {
        if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) {
            return self::bulk_delete_letters();
        }

        if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'cancel' ) {
            return self::cancel_scheduled_letter();
        }

        if ( isset( $_REQUEST['id'] ) ) {
            $prepared = self::$subscribe_db->get_row( sprintf( 'SELECT * FROM letters WHERE id = %d', absint( $_REQUEST['id'] ) ), ARRAY_A );

            if ( $prepared === null || $prepared['status'] !== 'draft' ) {
                wp_safe_redirect( self::$admin_url, 302 );
                exit;
            }

            self::$prepared = $prepared;
        }
    }

    /**
     * Process initial actions and prepare data on clicks subscribe tab
     */
    private static function prepare_clicks_actions() {
        if ( isset( $_REQUEST['id'] ) ) {
            $prepared = self::$subscribe_db->get_row( sprintf( 'SELECT * FROM letters WHERE id = %d', absint( $_REQUEST['id'] ) ), ARRAY_A );

            if ( $prepared === null ) {
                wp_safe_redirect( self::$admin_url, 302 );
                exit;
            }

            self::$prepared = $prepared;
        }
    }

    /**
     * Process initial actions and prepare data on users subscribe tab
     */
    private static function prepare_users_actions() {
        if ( isset( $_POST['action'] ) ) {
            return self::bulk_replace_users( sanitize_key( $_POST['action'] ) );
        }
    }

    /**
     * Handle bulk action for users table
     */
    private static function bulk_delete_letters() {
        check_admin_referer( 'bulk-subscribe-letters' );

        if ( isset( $_REQUEST['id'] ) ) {
            $ids = wp_parse_id_list( wp_unslash( $_REQUEST['id'] ) );
        }

        if ( count( $ids ) < 1 ) {
            return;
        }

        $ids = implode( ',', $ids );
        self::$subscribe_db->query( "DELETE FROM letters WHERE id IN({$ids})" );

        return self::redirect_with_message( 3 );
    }

    /**
     * Cancel scheduled letter
     */
    private static function cancel_scheduled_letter() {
        if ( empty( $_REQUEST['id'] ) ) {
            return;
        }

        $id = absint( $_REQUEST['id'] );
        self::$subscribe_db->query( "UPDATE letters SET status = 'draft' WHERE id = {$id}" );

        return self::redirect_with_message( 7 );
    }

    /**
     * Handle bulk action for letters table
     */
    private static function bulk_replace_users( $action ) {
        check_admin_referer( 'bulk-subscribe-users' );

        $valid = array( 'active', 'unsubscribed', 'block' );

        if ( ! in_array( $action, $valid, true ) ) {
            return;
        }

        if ( isset( $_POST['id'] ) ) {
            $ids = wp_parse_id_list( wp_unslash( $_POST['id'] ) );
        }

        if ( count( $ids ) < 1 ) {
            return;
        }

        $ids = implode( ',', $ids );
        self::$subscribe_db->query( "UPDATE users SET status = '{$action}' WHERE id IN({$ids})" );

        return self::redirect_with_message( 5, array( 'tab' => 'users' ) );
    }

    /**
     * Get analytics data for stats tab
     */
    private static function get_analytics_data( $id ) {
        $prepare = self::$subscribe_db->prepare(
            'SELECT reference, COUNT(id) as amount FROM actions WHERE reference <> "" AND letters_id = %d GROUP BY reference',
            $id
        );

        return self::$subscribe_db->get_results( $prepare, ARRAY_A );
    }

    /**
     * Get summary data for default tab
     */
    private static function get_default_summary() {
        $query =
            'SELECT  (
                SELECT COUNT(id) FROM users WHERE status = "active"
            ) AS active,
            (
                SELECT COUNT(id) FROM users
            ) AS total,
            (
                SELECT COUNT(id) FROM users WHERE status = "active" AND created >= DATE(NOW() - INTERVAL 7 DAY)
            ) AS new_week,
            (
                SELECT COUNT(id) FROM users WHERE status = "active" AND created >= DATE(NOW() - INTERVAL 1 MONTH)
            ) AS new_month,
            (
                SELECT COUNT(id) FROM actions WHERE action = "unsubscribe" AND created >= DATE(NOW() - INTERVAL 7 DAY)
            ) AS left_week,
            (
                SELECT COUNT(id) FROM actions WHERE action = "unsubscribe" AND created >= DATE(NOW() - INTERVAL 1 MONTH)
            ) AS left_month,
            (
                SELECT ROUND(SUM(action = "open") / SUM(action = "received") * 100) FROM actions
            ) AS avg_open,
            (
                SELECT ROUND(SUM(action = "click") / SUM(action = "received") * 100) FROM actions
            ) AS avg_click';

        return self::$subscribe_db->get_row( $query, ARRAY_A );
    }

    /**
     * Set letter status as scheduled
     */
    private static function schedule_letter( $id ) {
        if ( empty( $_POST['released'] ) ) {
            return self::redirect_with_message( 6, array( 'id' => $id ) );
        }

        // phpcs:ignore
        $released = strtotime( wp_unslash( $_POST['released'] ) );

        if ( empty( $released ) ) {
            return self::redirect_with_message( 6, array( 'id' => $id ) );
        }

        $args = array(
            'status'   => 'scheduled',
            'released' => gmdate( 'Y-m-d H:i:s', $released ),
        );

        self::$subscribe_db->update( 'letters', $args, array( 'id' => $id ) );

        return self::redirect_with_message( 4, $args );
    }

    /**
     * Custom redirect method with message
     */
    private static function redirect_with_message( $message, $args = array() ) {
        $args['message'] = $message;

        wp_safe_redirect( add_query_arg( $args, self::$admin_url ), 302 );
        exit;
    }
}


/**
 * Load current module environment
 */
Knife_Subscribe_Manager::load_module();
