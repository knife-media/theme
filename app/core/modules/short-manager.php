<?php
/**
 * Short manager
 *
 * Return short links manager with simple stat
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Short_Manager {
    /**
     * Management page slug
     *
     * @access  public
     * @var     string
     */
    public static $page_slug = 'knife-short';

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
    private static $per_page = 'knife_short_per_page';

    /**
     * Availavle short hosts
     */
    private static $hosts = array( 'knf.md', 'tgrs.link' );

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action( 'admin_menu', array( __CLASS__, 'add_management_page' ), 20 );

        // Init short links action before page load
        add_action( 'current_screen', array( __CLASS__, 'init_short_actions' ) );

        // Admin-post action to add similar link
        add_action( 'admin_post_' . self::$page_slug . '-append', array( __CLASS__, 'append_short_link' ) );

        // Save links per page screen option
        add_filter( 'set-screen-option', array( __CLASS__, 'save_screen_options' ), 10, 3 );

        // Define short links settings if still not
        if ( ! defined( 'KNIFE_SHORT' ) ) {
            define( 'KNIFE_SHORT', array() );
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
            esc_html__( 'Сокращатель ссылок', 'knife-theme' ),
            esc_html__( 'Сокращатель ссылок', 'knife-theme' ),
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
    public static function init_short_actions() {
        $current_screen = get_current_screen();

        if ( $current_screen->base !== self::$screen_base ) {
            return;
        }

        // Add scripts to admin page
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        // Add screen options
        add_action( 'load-' . self::$screen_base, array( __CLASS__, 'add_screen_options' ) );
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
                    'knife-short-actions',
                    'append',
                    esc_html__( 'Короткий адрес уже существует', 'knife-theme' )
                );
                break;
            case 2:
                add_settings_error(
                    'knife-short-actions',
                    'append',
                    esc_html__( 'Ссылка успешно добавлена', 'knife-theme' ),
                    'updated'
                );
                break;
            case 3:
                add_settings_error(
                    'knife-short-actions',
                    'append',
                    esc_html__( 'Не удалось добавить ссылку', 'knife-theme' )
                );
                break;
        }

        $include = get_template_directory() . '/core/include';

        // Include Short Links table class
        include_once $include . '/tables/short-links.php';

        $db = self::connect_short_db();

        // Get short links table instance
        $table = new Knife_Short_Links_Table( $db, self::$per_page );

        $table->process_actions();
        $table->prepare_items();

        // Include options template to show table
        include_once $include . '/templates/short-options.php';
    }

    /**
     * Enqueue assets to admin certain screen only
     */
    public static function enqueue_assets() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style( 'knife-short-options', $include . '/styles/short-options.css', array(), $version );

        // Insert admin scripts
        wp_enqueue_script( 'knife-short-options', $include . '/scripts/short-options.js', array( 'jquery' ), $version, true );

        if ( ! property_exists( 'Knife_Promo_Manager', 'prefix_adfox' ) ) {
            return;
        }

        if ( ! property_exists( 'Knife_Promo_Manager', 'prefix_ord' ) ) {
            return;
        }

        $options = array(
            'selector'    => self::$page_slug . '-convert',
            'source'      => esc_html( Knife_Promo_Manager::$prefix_adfox ),
            'destination' => esc_html( Knife_Promo_Manager::$prefix_ord ),
        );

        wp_localize_script( 'knife-short-options', 'knife_short_options', $options );
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
    private static function connect_short_db() {
        // Mix with default values
        $conf = wp_parse_args(
            KNIFE_SHORT,
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
     * Append short link
     */
    public static function append_short_link() {
        check_admin_referer( 'knife-short-append' );

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этой странице', 'knife-theme' ) );
        }

        $admin_url = admin_url( '/tools.php?page=' . self::$page_slug );

        // Check if required values not empty
        if ( ! empty( $_POST['keyword'] ) && ! empty( $_POST['url'] ) ) {
            $remote_addr = '';

            if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
                $remote_addr = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
            }

            $data = array(
                'url' => trim( esc_url_raw( wp_unslash( $_POST['url'] ) ) ),
                'ip'  => $remote_addr,
            );

            $db = self::connect_short_db();

            if ( empty( $_POST['host'] ) || ! in_array( $_POST['host'], self::$hosts, true ) ) {
                wp_safe_redirect( add_query_arg( 'message', 3, $admin_url ), 302 );
                exit;
            }

            $data['host'] = sanitize_text_field( wp_unslash( $_POST['host'] ) );

            $data['keyword'] = self::get_link_keyword(
                trim( sanitize_key( $_POST['keyword'] ) ),
                $data['host'],
                $db
            );

            if ( $data['keyword'] === false ) {
                wp_safe_redirect( add_query_arg( 'message', 1, $admin_url ), 302 );
                exit;
            }

            $data['title'] = self::get_link_title( $data['url'] );

            if ( $db->insert( 'urls', $data ) ) {
                // Fix strage bug with wp_redirect here
                // It crashes with 502 on apply_filters('wp_redirect')
                // Logged error: 'upstream sent too big header while reading response header from upstream'
                header( 'Location: ' . add_query_arg( 'message', 2, $admin_url ) );
                exit;
            }
        }

        wp_safe_redirect( add_query_arg( 'message', 3, $admin_url ), 302 );
        exit;
    }

    /**
     * Check keyword existance
     */
    private static function get_link_keyword( $keyword, $host, $db ) {
        // Cut and replace dashes
        $keyword = str_replace( '_', '-', substr( $keyword, 0, 200 ) );

        // Make verification request
        $query = $db->prepare( 'SELECT id FROM urls WHERE keyword = %s AND host = %s', $keyword, $host );

        if ( $db->get_var( $query ) === null ) {
            return $keyword;
        }

        return false;
    }

    /**
     * Get page title
     *
     * Return post title if the url from this site
     * Else try to fecth page title using remote api
     */
    private static function get_link_title( $url ) {
        $post_id = url_to_postid( $url );

        if ( $post_id > 0 ) {
            return esc_html( get_the_title( $post_id ) );
        }

        $response = wp_safe_remote_get(
            esc_url_raw( $url ),
            array(
                'timeout' => 3,
            )
        );

        $title = $url;

        // Try to fetch page content by url
        if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
            $content = wp_remote_retrieve_body( $response );

            // Find page title in the content
            if ( preg_match( '~<title[^>]*>(.+?)</title>~iU', $content, $string ) ) {
                $title = esc_html( trim( $string[1] ) );
            }
        }

        // Cut title if too long
        if ( mb_strlen( $title ) > 100 ) {
            $title = mb_substr( $title, 0, 100 ) . '…';
        }

        return $title;
    }
}


/**
 * Load current module environment
 */
Knife_Short_Manager::load_module();
