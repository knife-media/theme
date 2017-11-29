<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */


if(!function_exists('knife_custom_background')) :
/**
 * Prints class with thumbnail url in background-image style
 *
 * @since 1.1
 */

function knife_custom_background() {
	ob_start();

	_custom_background_cb();

	$style = ob_get_clean();
	$style = str_replace( 'body.custom-background', '.wrap', $style );

	echo $style;
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
	if(function_exists('get_coauthors'))
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


if(!function_exists('knife_theme_entry_share')) :
/**
 * Prints share buttons
 *
 * TODO: don't forget to rework this
 *
 * @since 1.1
 */

function knife_theme_entry_share() {
?>
<div class="entry__share">
	<p class="entry__share-title"><?php _e('Поделиться в соцсетях:', 'knife-theme'); ?></p>

	<div class="entry__share-list">
		<a class="entry__share-item entry__share-item--facebook" href="http://www.facebook.com/sharer/sharer.php?p[url]=<?php the_permalink(); ?>&p[title]=<?php the_title(); ?>" target="_blank">
			<span class="icon icon--facebook"></span>
			<span class="entry__share-action"><?php _e('Пошерить', 'knife-theme'); ?></span>
		</a>

		<a class="entry__share-item entry__share-item--vkontakte" href="http://vk.com/share.php?url=<?php the_permalink(); ?>" target="_blank">
			<span class="icon icon--vkontakte"></span>
			<span class="entry__share-action"><?php _e('Поделиться', 'knife-theme'); ?></span>
		</a>

		<a class="entry__share-item entry__share-item--telegram" href="https://t.me/share/url?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank">
			<span class="icon icon--telegram">
		</a>

		<a class="entry__share-item entry__share-item--twitter" href="https://twitter.com/intent/tweet?text=<?php the_title();?>&url=<?php the_permalink(); ?>" target="_blank">
			<span class="icon icon--twitter">
		</a>
	</div>
</div>
<?php
}

endif;


if(!function_exists('knife_theme_entry_header')) :
/**
 * Prints entry author, category and date
 *
 * @since 1.1
 */

function knife_theme_entry_header() {


	$category = get_the_category();

	if(isset($category[0])) {
		printf(
			'<a class="entry__header-meta" href="%1$s">%2$s</a>',
			esc_url(get_category_link($category[0]->term_id)),
			sanitize_text_field($category[0]->cat_name)
		);
	}
}

endif;


if(!function_exists('knife_theme_entry_related')) :
/**
 * Prints related posts by category
 *
 * TODO: Rework this
 *
 * @since 1.1
 */

function knife_theme_entry_related() {
	global $post;

	$cats = get_the_category();

	$base = [
		'post__not_in' => [$post->ID],
		'posts_per_page' => 6,
 		'ignore_sticky_posts' => 1,
 		'post_status' => 'publish'
	];

	if(isset($cats[0]))
		$base['category__in'] = $cats[0]->cat_ID;

	$entry_related = new WP_Query($base);

	if($entry_related->have_posts()) {
		printf(
			'<div class="entry__related"><p class="entry__related-title">%s</p>',
			__('Читайте также:', 'knife-theme')
		);

		while($entry_related->have_posts()) {

			$entry_related->the_post();

			printf(
				'<div class="entry__related-item"><a class="entry__related-link" href="%1$s">%2$s</a></div>',
				get_the_permalink(),
				get_the_title()
			);
		}

		wp_reset_postdata();

		print('</div>');
	}
}

endif;



if(!function_exists('knife_theme_category_link')) :
/**
 * Single category link with arg class
 *
 * @since 1.1
 */
function knife_theme_category_link($link_class = '') {
	$category = get_the_category();

	if(!isset($category[0]))
		return '';

	return sprintf(
		'<a class="%1$s" href="%2$s">%3$s</a>',
		esc_attr($link_class),
		esc_url(get_category_link($category[0]->term_id)),
		sanitize_text_field($category[0]->cat_name)
	);
}

endif;



if(!function_exists('knife_theme_tags')) :
/**
 * Current post tag list without links
 *
 * @since 1.1
 */
function knife_theme_tags($count = 3, $echo = true) {
	if($tags = get_the_tags()) {

		$list = implode(', ', wp_list_pluck(array_slice($tags, 0, $count), 'name'));

		if($echo === false)
			return $list;

		echo $list;
	}
}

endif;



if(!function_exists('knife_theme_entry_tags')) :
/**
 * Prints entry tags
 *
 * @since 1.1
 */

function knife_theme_entry_tags() {
	$tags = get_the_tags();

	if($tags) {
		print('<div class="entry__tags">');

		foreach($tags as $tag) {
			printf(
				'<a class="entry__tags-link" href="%1$s">%2$s</a>',
				esc_url(get_tag_link($tag->term_id)),
				sanitize_text_field($tag->name)
			);
		}

		print('</div>');
	}
}

endif;
