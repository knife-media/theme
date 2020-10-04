<?php
/**
 * avito: special functions
 *
 * @package knife-theme
 * @since 1.14
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = basename(__DIR__);

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


/**
 * Add button to post content
 */
add_action('the_content', function($content) {
    global $post;

    $slug = basename(__DIR__);

    if(!property_exists('Knife_Special_Projects', 'taxonomy')) {
        return $output;
    }

    $taxonomy = Knife_Special_Projects::$taxonomy;

    if(has_term($slug, $taxonomy, $post) && is_main_query() && is_singular('post')) {
        $promo_link = sprintf(
            '<figure class="figure figure--promo"><a class="button" href="%s">%s <strong>%s</strong></a>',
            esc_url(get_term_link($slug, $taxonomy)),
            _x('Специальный проект', 'special: avito', 'knife-theme'),
            _x('Журнала «Нож» и «Авито Недвижимости»', 'special: avito', 'knife-theme')
        );

        $content = $content . $promo_link;
    }

    return $content;
});
