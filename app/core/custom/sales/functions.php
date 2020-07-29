<?php
/**
 * sales: custom fucntions
 *
 * @package knife-theme
 * @since 1.13
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Define current custom slug
 */
define('KNIFE_CUSTOM_SLUG', 'sales');


/**
 * Add custom styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = KNIFE_CUSTOM_SLUG;

    // Get styles
    $styles = "/core/custom/{$slug}/styles.css";

    // Get scripts
    $scripts = "/core/custom/{$slug}/scripts.js";

    // Set styles version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Let's add styles
    wp_enqueue_style('knife-custom-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);

    // Let's add scripts
    wp_enqueue_script('knife-custom-' . $slug, get_template_directory_uri() . $scripts, ['knife-theme'], $version, true);

    $options = [
        'formats' => _x('Больше проектов', 'custom: sales more button', 'knife-theme')
    ];

    wp_localize_script('knife-custom-' . $slug, 'knife_theme_custom', $options);
});
