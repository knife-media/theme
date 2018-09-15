<?php
/**
* Custom theme shortcodes
*
* List of all theme shortcodes with definitions
*
* @package knife-theme
* @since 1.1
*/


/**
 * Add shortcode for cards posts.
 * Usually uses on chat posts format
 */
add_shortcode('card', function($atts, $content = null) {
    if($content === null) {
        return;
    }

    $html = sprintf(
        '<div class="entry-content">%s</div>',
        do_shortcode(force_balance_tags($content))
    );

    return $html;
}, 10, 2);
