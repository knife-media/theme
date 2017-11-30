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
