<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.14
 */


/**
 * Add custom advertisment script for mts-switcher promo
 */
add_action('wp_enqueue_scripts', function() {
    // Get theme version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');

        // Add scripts
        wp_enqueue_script('knife-mts', '/external/mts-switcher/scripts.min.js', [], $version, true);

        // Add styles
        wp_enqueue_style('knife-mts', '/external/mts-switcher/styles.min.css', [], $version);
    }
});
