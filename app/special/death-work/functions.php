<?php
/**
 * Death work: functions
 *
 * @package knife-theme
 * @since 1.12
 */


/**
 * Replace the title in prev post button with hero name
 */
add_action('previous_post_link', function($output, $format, $link, $prev) {
    global $post;

    if(has_term('death-work', 'special', $post) && isset($prev->ID)) {
        // Find hero name post meta
        $hero = get_post_meta($prev->ID, 'post-info', true);

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

    if(has_term('death-work', 'special', $post) && isset($next->ID)) {
        // Find hero name post meta
        $hero = get_post_meta($next->ID, 'post-info', true);

        if($hero) {
            $output = str_replace(get_the_title($next->ID), $hero, $output);
        }
    }

    return $output;
}, 10, 4);
