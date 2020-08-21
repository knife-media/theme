<?php
/**
 * black: special functions
 *
 * @package knife-theme
 * @since 1.13
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = basename(__DIR__);
    //$data = get_file_data(__FILE__, ['slug' => 'slug']);
    //$slug = $data['slug'];

    // Get theme version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    $styles = "/core/special/{$slug}/styles.css";

    // Let's add the file if exists
    if(file_exists(get_template_directory() . $styles)) {
        wp_enqueue_style('knife-special-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);
    }
});


/**
 * Set template for archive posts
 */
add_action('archive_template', function($template) {
    $slug = basename(__DIR__);

    // Locate single template
    $new_template = locate_template(["core/special/{$slug}/archive.php"]);

    if(empty($new_template)) {
        $new_template = $template;
    }

    return $new_template;
});
