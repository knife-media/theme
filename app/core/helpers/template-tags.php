<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */



if(!function_exists('knife_theme_meta')) :
/**
 * Prints post meta info
 *
 * Post date, authors, category and optional tags
 *
 * @since 1.1
 */
function knife_theme_meta($args, $html = '') {
    $defaults = [
        'opts' => ['author', 'date', 'category'],
        'before' => '',
        'after' => '',
        'item' => '<span class="meta__item">%s</span>',
        'link' => '<a class="meta__link" href="%2$s">%1$s</a>',
        'is_link' => true,
        'echo' => true
    ];

    $args = wp_parse_args($args, $defaults);

    foreach($args['opts'] as $item) {
        switch($item) {

            // Prints post author
            case 'author':
                if($args['is_link'] === false) :
                    if(function_exists('coauthors'))
                        $html .= sprintf($args['item'], coauthors('', '', null, null, false));
                    else
                        $html .= sprintf($args['item'], get_the_author());
                else :
                    if(function_exists('coauthors_posts_links'))
                        $html .= coauthors_posts_links('', '', null, null, false);
                    else
                        $html .= get_the_author_posts_link();
                endif;
            break;

            // Prints post category
            case 'category':
                $cats = get_the_category();

                if(!isset($cats[0]))
                    break;

                if($args['is_link'] === false) :
                    $html .= sprintf($args['item'], sanitize_text_field($cats[0]->cat_name));
                else :
                    $html .= sprintf($args['link'],
                        sanitize_text_field($cats[0]->cat_name),
                        esc_url(get_category_link($cats[0]->term_id))
                    );
                endif;
            break;

            // Prints post publish date. Exclude current year
            case 'date':
                $date = sprintf('<time datetime="%1$s">%2$s</time>',
                    get_the_time('c'),
                    get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
                );

                 $html .= sprintf($args['item'], $date);
            break;

            // Prints only post publish time. Useful for news
            case 'time':
                 $html .= sprintf($args['item'], get_the_time('H:i'));
            break;

            //  Prints post single tag
            case 'tag' :
                if($args['is_link'] === false) :
                    $html .= knife_theme_tags(['item' => '%1$s', 'count' => 1, 'echo' => false]);
                else :
                    $html .= knife_theme_tags(['item' => $args['link'], 'count' => 1, 'echo' => false]);
                endif;
            break;

            // Same as above but for 3 tags
            case 'tags' :
                if($args['is_link'] === false) :
                    $html .= knife_theme_tags(['item' => '%1$s', 'count' => 3, 'echo' => false, 'between' => '']);
                else :
                    $html .= knife_theme_tags(['item' => $args['link'], 'count' => 3, 'echo' => false, 'between' => '']);
                endif;
            break;

            // Show widget head using widget query vars
            case 'head' :
                $title = get_query_var('widget_title', '');
                $link = get_query_var('widget_link', '');

                if(empty($title))
                    break;

                if(empty($link)) :
                    $html .= sprintf($args['item'], sanitize_text_field($title));
                else:
                    $html .= sprintf($args['link'], sanitize_text_field($title), esc_url($link));
                endif;
            break;

            // Show post type badge if exists
            case 'type' :
                $inherit = ['post', 'page', 'attachmenet'];
                $type = get_post_type(get_the_ID());

                if(in_array($type, $inherit))
                    break;

                $html .= sprintf('<a class="meta__link meta__link--%3$s" href="%2$s">%1$s</a>',
                    sanitize_text_field(get_post_type_object($type)->labels->name),
                    esc_url(get_post_type_archive_link($type)),
                    esc_attr($type)
                );
            break;
        }
    }

    $html = $args['before'] . $html . $args['after'];

      // Filter result html before return
    $html = apply_filters('knife_theme_meta', $html, $args);

    if($args['echo'] === false)
        return $html;

    echo $html;
}

endif;


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
