<?php
/**
 * black: custom fucntions
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
define('KNIFE_CUSTOM_SLUG', 'black');


/**
 * Add styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = KNIFE_CUSTOM_SLUG;

    // Get theme version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    $styles = "/core/custom/{$slug}/styles.css";

    // Let's add the file if exists
    if(file_exists(get_template_directory() . $styles)) {
        wp_enqueue_style('knife-custom-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);
    }
});


/**
 * Set template for archive posts
 */
add_action('archive_template', function($template) {
    $slug = KNIFE_CUSTOM_SLUG;

    // Locate single template
    $new_template = locate_template(["core/custom/{$slug}/archive.php"]);

    if(empty($new_template)) {
        $new_template = $template;
    }

    return $new_template;
});
