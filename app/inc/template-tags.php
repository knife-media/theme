<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */

if(!function_exists('knife_theme_cover')) :
/**
 * Prints class with thumnail url in background-image style
 *
 * @since 1.1
 */

function knife_theme_cover($class, $post_id = null) {
	$thumbnail = get_the_post_thumbnail_url($post->ID, 'fullscreen-thumbnail');

	if($thumbnail === false)
		return printf('class="%1$s"', $class);

	printf('class="%1$s %1$s--fullscreen" style="background-image:url(%2$s)"', $class, esc_url($thumbnail));
}

endif;


if(!function_exists('knife_theme_authors')) :
/**
 * Returns list of authors or co-autors without link
 *
 * Since we use co-authors plugin we have to use own fallback
 *
 * @since 1.1
 */

function knife_theme_authors() {
	if(function_exists('coauthors'))
		return coauthors();

	return the_author();
}

endif;


if(!function_exists('knife_theme_authors_links')) :
/**
 * Returns list of authors or co-autors with posts links
 *
 * Since we use co-authors plugin we have to use own fallback
 *
 * @since 1.1
 */

function knife_theme_authors_links() {
	if(function_exists('coauthors_posts_links'))
		return coauthors_posts_links();

	return the_author_posts_link();
}

endif;
