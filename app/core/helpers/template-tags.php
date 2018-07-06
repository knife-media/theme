<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */



if(!function_exists('knife_theme_widget_options')) :
/**
 * Merge and prints widget options as classes
 *
 * @since 1.1
 */
function knife_theme_widget_options($base = 'widget__item', $post_id = null) {
    global $post;

    $post_id = $post_id ?? $post->ID;
    $options = [$base];

    switch(get_query_var('widget_cover', 'default')) {
        case 'cover':
            $options[] = $base . '--cover';

            break;

        case 'nocover':
            break;

        default:
            if(!get_post_meta($post_id, '_knife-cover', true))
                break;

            $options[] = $base . '--cover';
    }

    $html = join(' ', $options);

    // Filter result html before return
    $html = apply_filters('knife_theme_widget_options', $html);

    echo $html;
}

endif;


if(!function_exists('knife_theme_widget_template')) :
/**
 * Helper function to wrap template part with custom parent tag
 *
 * @since 1.1
 */
function knife_theme_widget_template($args = []) {
    global $wp_query;

    $defaults = [
        'size' => null,
        'before' => '<div class="widget widget-%s">',
        'after' => '</div>'
    ];

    $args = wp_parse_args($args, $defaults);

    $opts = function($current, $found) use (&$args) {
        if($found < 3 || $current % 5 === 3 || $current % 5 === 4)
            return 'double';

        return 'triple';
    };


    if($args['size'] === null)
        $args['size'] = $opts($wp_query->current_post, (int) $wp_query->found_posts);


    printf($args['before'], esc_attr($args['size']));

    get_template_part('template-parts/widgets/' . $args['size']);

    echo $args['after'];
}

endif;
