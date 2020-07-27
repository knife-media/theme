<?php
/**
 * Sales: functions
 *
 * @package knife-theme
 * @since 1.13
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add custom styles
 */
add_action('wp_enqueue_scripts', function() {
    $name = 'sales';

    // Get styles
    $styles = "/core/customs/{$name}/styles.css";

    // Set styles version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Let's add the file
    wp_enqueue_style('knife-theme-' . $name, get_template_directory_uri() . $styles, ['knife-theme'], $version);
});
