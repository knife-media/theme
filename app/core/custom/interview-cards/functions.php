<?php
/**
 * interview-cards: custom fucntions
 *
 * @package knife-theme
 * @since 1.14
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Define current custom slug
 */
define('KNIFE_CUSTOM_SLUG', 'interview-cards');


/**
 * Add custom styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = KNIFE_CUSTOM_SLUG;

    // Get styles
    $styles = "/core/custom/{$slug}/styles.css";

    // Set styles version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Let's add the file
    wp_enqueue_style('knife-custom-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);
});
