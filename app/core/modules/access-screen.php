<?php
/**
 * Custom login screen
 *
 * Styling wp-login.php page
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Access_Screen {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_filter( 'login_headerurl', array( __CLASS__, 'change_url' ) );
        add_filter( 'login_headertext', array( __CLASS__, 'change_title' ) );

        // login styles
        add_filter( 'login_enqueue_scripts', array( __CLASS__, 'login_styles' ) );

        // admin styles
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_styles' ) );
    }

    /**
     * Prints custom styles with custom logo
     */
    public static function login_styles() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_style( 'knife-access-screen-login', $include . '/styles/access-screen-login.css', array(), $version );
    }

    /**
     * We have to style login layer on auth-check
     */
    public static function admin_styles() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_style( 'knife-access-screen-admin', $include . '/styles/access-screen-admin.css', array(), $version );
    }

    /**
     * Change logo links to front page instead of wordpress.org
     */
    public static function change_url() {
        return home_url();
    }

    /**
     * Change logo title
     */
    public static function change_title() {
        return esc_html__( 'На главную', 'knife-theme' );
    }
}

/**
 * Load current module environment
 */
Knife_Access_Screen::load_module();
