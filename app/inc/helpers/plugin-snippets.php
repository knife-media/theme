<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 */


/**
 * Add custom meta class to couathors posts link
 *
 * @link https://github.com/Automattic/Co-Authors-Plus/blob/master/template-tags.php#L272
 */

add_filter('coauthors_posts_link', function($args) {
	$args['class'] = 'meta__link';

	return $args;
});


/**
 * Add custom text and font for social image generator
 *
 * @link https://github.com/antonlukin/social-image/blob/master/src/classes/Generation.php
 */
add_filter('social_image_texts', function($texts, $params) {
	$fonts = get_template_directory() . '/assets/fonts/';

	$texts = [
		[
			"text" => 'KNIFE.MEDIA',
			"posx" => 65,
			"posy" => 60,
			"file" => $fonts . 'formular/formular-black.ttf',
			"size" => 22,
			"color" => '#ffe634'
		],

		[
			"text" => wordwrap($params['text'], 1024 / 20),
			"posx" => 65,
			"posy" => 150,
			"file" => $fonts . 'formular/formular-medium.ttf',
			"size" => 46,
			"color" => '#ffe634'
		]
	];

	return $texts;
}, 10, 2);
