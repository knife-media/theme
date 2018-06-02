<?php
/**
* Google custom search engine
*
* Set google custom search app id and posts count settings
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Google_Search;

class Knife_Google_Search {
    private $option = 'knife-search-settings';

    public function __construct() {
        // include Google Custom Search js sdk
        add_action('wp_enqueue_scripts', [$this, 'inject_object'], 12);

        // plugin settings
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'add_menu']);
    }


    /**
     * Include app id for Google Custom Search API to knife-theme js script
     */
    public function inject_object() {
        $opts = get_option($this->option);

        if(empty($opts['appid']))
            return false;

        wp_localize_script('knife-theme', 'knife_search_id', $opts['appid']);
    }


    /**
     * Add push settings submenu to main options menu
     */
    public function add_menu() {
        add_submenu_page('options-general.php', __('Настройка поиска', 'knife-theme'), __('Поиск по сайту', 'knife-theme'), 'manage_options', 'knife-search', [$this, 'settings_page']);
    }


    /**
     * Display push options page
     */
     public function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-search-settings');
        do_settings_sections('knife-sarch-settings');
        submit_button();

        echo '</form>';
    }


    /**
     * Register settings forms
     */
    public function settings_init() {
        register_setting('knife-search-settings', $this->option);

        add_settings_section(
            'knife-search-section',
            __('Настройка поиска', 'knife-theme'),
            [],
            'knife-search-settings'
        );

        add_settings_field(
            'appid',
            __('Google Custom Search ID', 'knife-theme'),
            [$this, 'setting_render_appid'],
            'knife-search-settings',
             'knife-search-section'
        );
    }

    public function setting_render_appid() {
        $options = get_option($this->option);
        $default = isset($options['appid']) ? $options['appid'] : '';

        printf(
            '<input type="text" name="%1$s[appid]" class="widefat" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }
}
