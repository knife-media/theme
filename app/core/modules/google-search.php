<?php
/**
 * Google custom search engine
 *
 * Set google custom search app id and posts count settings
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.10
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
    private static $option_id = 'knife_search_id';


    /**
     * Init function instead of constructor
     *
     * @since 1.4
     */
    public static function load_module() {
        // Include Google Custom Search js sdk
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Plugin settings
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);

        // Disable default search
        add_action('parse_query', [__CLASS__, 'disable_search'], 9);

        // Add search popover to footer
        add_action('wp_footer', function() {
            get_search_form();
        });
    }


    /**
     * Include app id for Google Custom Search API to knife-theme js script
     */
    public static function inject_object() {
        $search_id = get_theme_mod(self::$option_id);

        if(strlen($search_id) > 0) {
            wp_localize_script('knife-theme', 'knife_search_id', $search_id);
        }
    }


    /**
     * Disable wordpress based search to reduce CPU load and prevent DDOS attacks
     */
    public static function disable_search($query) {
        if(!is_admin() && $query->is_search()) {
            $query->set('s', '');
            $query->is_search = false;
            $query->is_404 = true;
        }
    }


    /**
     * Save GCSE id to theme option
     *
     * Replace old settings controls within options page
     *
     * @since 1.4
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$option_id);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$option_id, [
                 'label'      => __('Google Custom Search ID', 'knife-theme'),
                 'section'    => 'title_tagline'
             ]
        ));
    }

}


/**
 * Load current module environment
 */
Knife_Google_Search::load_module();
