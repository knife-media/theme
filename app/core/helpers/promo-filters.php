<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.12
 */


add_action('wp_enqueue_scripts', function() {
    // Get theme version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Let's add the file if exists
    wp_enqueue_script('knife-mts', 'https://knife.support/static/mts-switcher/scripts.min.js', [], $version, true);
});
