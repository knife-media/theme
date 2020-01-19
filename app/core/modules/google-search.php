<?php
/**
 * Google custom search engine
 *
 * Set google custom search app id and posts count settings
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.12
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Google_Search {
    /**
     * Init function instead of constructor
     *
     * @since 1.4
     */
    public static function load_module() {
        // Include Google Custom Search js sdk
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Disable default search
        add_action('parse_query', [__CLASS__, 'disable_search'], 9);

        // Add search popover to footer
        add_action('wp_footer', function() {
            get_search_form();
        });

        // Define google search settings if still not
        if(!defined('KNIFE_GCSE')) {
            define('KNIFE_GCSE', []);
        }
    }


    /**
     * Include app id for Google Custom Search API to knife-theme js script
     */
    public static function inject_object() {
        if(!empty(KNIFE_GCSE['id'])) {
            wp_localize_script('knife-theme', 'knife_search_id', KNIFE_GCSE['id']);
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
}


/**
 * Load current module environment
 */
Knife_Google_Search::load_module();
