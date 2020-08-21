<?php
/**
 * lighthouse: special functions
 *
 * @package knife-theme
 * @since 1.10
 * @version 1.13
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
 * Set template for archive posts
 */
add_action('single_template', function($template) {
    $slug = basename(__DIR__);

    // Locate single template
    $new_template = locate_template(["core/special/{$slug}/single.php"]);

    if(empty($new_template)) {
        $new_template = $template;
    }

    return $new_template;
});


/**
 * Replace the title in prev post button with hero name
 */
add_action('previous_post_link', function($output, $format, $link, $prev) {
    global $post;

    $slug = basename(__DIR__);

    if(!property_exists('Knife_Special_Projects', 'taxonomy')) {
        return $output;
    }

    $taxonomy = Knife_Special_Projects::$taxonomy;

    if(has_term($slug, $taxonomy, $post)) {
        // Check if previous post exists
        if(empty($prev->ID)) {
            $posts = get_posts([
                $taxonomy => $slug,
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);

            // Check post exists and not same as current
            if(empty($posts[0]->ID) || $posts[0]->ID === $post->ID) {
                return $output;
            }

            $prev = $posts[0];

            // Generate default output
            $output = sprintf('<a href="%s" rel="prev">%s</a>',
                get_permalink($prev->ID),
                get_the_title($prev->ID)
            );
        }

        // Find hero name post meta
        $hero = get_post_meta($prev->ID, 'post-hero', true);

        if($hero) {
            $output = str_replace(get_the_title($prev->ID), $hero, $output);
        }
    }

    return $output;
}, 10, 4);


/**
 * Replace the title in next post button with hero name
 */
add_action('next_post_link', function($output, $format, $link, $next) {
    global $post;

    $slug = basename(__DIR__);

    if(!property_exists('Knife_Special_Projects', 'taxonomy')) {
        return $output;
    }

    $taxonomy = Knife_Special_Projects::$taxonomy;

    if(has_term($slug, $taxonomy, $post)) {
        // Check if next post exists
        if(empty($next->ID)) {
            $posts = get_posts([
                $taxonomy => $slug,
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'ASC'
            ]);

            // Check post exists and not same as current
            if(empty($posts[0]->ID) || $posts[0]->ID === $post->ID) {
                return $output;
            }

            $next = $posts[0];

            // Generate default output
            $output = sprintf('<a href="%s" rel="next">%s</a>',
                get_permalink($next->ID),
                get_the_title($next->ID)
            );
        }

        // Find hero name post meta
        $hero = get_post_meta($next->ID, 'post-hero', true);

        if($hero) {
            $output = str_replace(get_the_title($next->ID), $hero, $output);
        }
    }

    return $output;
}, 10, 4);
