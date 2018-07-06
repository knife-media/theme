<?php
/**
* Custom login screen
*
* Styling wp-login.php page
*
* @package knife-theme
* @since 1.2
*/

if (!defined('WPINC')) {
    die;
}


new Knife_Access_Screen;

class Knife_Access_Screen {
    public function __construct() {
        add_action('login_headerurl', [$this, 'change_url']);
        add_action('login_headertitle', [$this, 'change_title']);

        // login styles
        add_filter('login_enqueue_scripts', [$this, 'login_styles']);

        // admin styles
        add_action('admin_enqueue_scripts', [$this, 'admin_styles']);
    }

    /**
     * Prints custom styles with custom logo
     */
    public function login_styles() {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_style('knife-access-screen-login', $include . '/styles/access-screen-login.css', [], $version);
    }


    /**
     * We have to style login layer on auth-check
     */
    public function admin_styles() {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_style('knife-access-screen-admin', $include . '/styles/access-screen-admin.css', [], $version);
    }

    /**
     * Change logo links to front page instead of wordpress.org
     */
    public function change_url() {
        return home_url();
    }

    /**
     * Change logo title
     */
    public function change_title() {
        return __('На главную', 'knife-theme');
    }
}
