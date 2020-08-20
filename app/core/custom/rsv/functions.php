<?php
/**
 * rsv: functions
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
define('KNIFE_CUSTOM_SLUG', 'rsv');


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


/**
 * Add return button to post content
 */
add_action('the_content', function($content) {
    global $post;

    $slug = KNIFE_CUSTOM_SLUG;

    if(!property_exists('Knife_Special_Projects', 'taxonomy')) {
        return $output;
    }

    $taxonomy = Knife_Special_Projects::$taxonomy;

    if(has_term($slug, $taxonomy, $post) && is_main_query()) {
        $promo_link = sprintf(
            '<figure class="figure figure--promo"><a class="button" href="%s">%s</a>',
            esc_url(get_term_link($slug, $taxonomy)),
            _x('Совместный проект платформы «Россия — страна возможностей» и журнала «Нож»', 'custom: rsv', 'knife-theme'),
        );

        $content = $content . $promo_link;
    }

    return $content;
});