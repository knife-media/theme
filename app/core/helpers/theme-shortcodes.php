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
	if($content === null)
		return;

	$html = sprintf(
		'<div class="post__card">%s</div>',
		do_shortcode(force_balance_tags($content))
	);

	return apply_filters('knife_theme_shortcode_card', $html, $atts);
}, 10, 2);
