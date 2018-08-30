<?php
/**
* Google custom search engine
*
* Set google custom search app id and posts count settings
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Google_Search {
    /**
     * Option to store search settings
     *
     * @access  private
     * @var     string
     */
    private static $option = 'knife-search-settings';


    /**
     * Init function instead of constructor
     *
     * @since 1.4
     */
    public static function load_module() {
        // Include Google Custom Search js sdk
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Plugin settings
        add_action('admin_init', [__CLASS__, 'settings_init']);
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }


    /**
     * Include app id for Google Custom Search API to knife-theme js script
     */
    public static function inject_object() {
        $opts = get_option(self::$option);

        if(empty($opts['appid'])) {
            return false;
        }

        wp_localize_script('knife-theme', 'knife_search_id', $opts['appid']);
    }


    /**
     * Add push settings submenu to main options menu
     */
    public static function add_menu() {
        add_submenu_page('options-general.php', __('Настройка поиска', 'knife-theme'), __('Поиск по сайту', 'knife-theme'), 'manage_options', 'knife-search', [__CLASS__, 'settings_page']);
    }


    /**
     * Display push options page
     */
     public static function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-search-settings');
        do_settings_sections('knife-search-settings');
        submit_button();

        echo '</form>';
    }


    /**
     * Register settings forms
     */
    public static function settings_init() {
        register_setting('knife-search-settings', self::$option);

        add_settings_section(
            'knife-search-section',
            __('Настройка поиска', 'knife-theme'),
            [],
            'knife-search-settings'
        );

        add_settings_field(
            'appid',
            __('Google Custom Search ID', 'knife-theme'),
            [__CLASS__, 'setting_render_appid'],
            'knife-search-settings',
            'knife-search-section'
        );
    }

    public static function setting_render_appid() {
        $options = get_option(self::$option);
        $default = isset($options['appid']) ? $options['appid'] : '';

        printf(
            '<input type="text" name="%1$s[appid]" class="widefat" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }
}


/**
 * Load current module environment
 */
Knife_Google_Search::load_module();
