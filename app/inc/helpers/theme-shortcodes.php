<?php
/**
* Custom theme shortcodes
*
* List of all theme shortcodes with definitions
*
* @package knife-theme
* @since 1.1
*/

new Knife_Theme_Shortcodes;

class Knife_Theme_Shortcodes {
	public function __construct() {
		add_shortcode('card', [$this, 'card'], 10, 2);
	}

	public function card($atts, $content = null) {
		if($content === null)
			return;

		$html = sprintf(
			'<div class="post__card">%s</div>',
			do_shortcode($content)
		);

		return apply_filters('knife_theme_shortcode_card', $html, $atts);
	}
}
