<?php
/**
 * argue-cards: custom fucntions
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.13
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Define current custom slug
 *
 * @since 1.13
 */
define('KNIFE_CUSTOM_SLUG', 'argue-cards');


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
