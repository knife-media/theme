<?php

if (!defined('WPINC')) {
    die;
}


/**
 * Add custom styles
 */
add_action('wp_enqueue_scripts', function() {
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    $slug = get_query_var('customs_slug');

    // Get styles
    $styles = "/core/customs/{$slug}/styles.css";

    // Let's add the file
    wp_enqueue_style('knife-theme-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);
});
