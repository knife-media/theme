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
	$thumbnail = get_the_post_thumbnail_url($post_id, 'fullscreen-thumbnail');

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


if(!function_exists('knife_theme_entry_share')) :
/**
 * Prints share buttons
 *
 * @since 1.1
 */

function knife_theme_entry_share() {
?>
<div class="entry__share">
	<p class="entry__share-title"><?php _e('Поделиться в соцсетях:', 'knife-theme'); ?></p>

	<div class="entry__share-list">
		<a class="entry__share-item entry__share-item--fb" href="http://www.facebook.com/sharer/sharer.php?p[url]=<?php the_permalink(); ?>&p[title]=<?php the_title(); ?>" target="_blank">
			<span class="icon icon--fb"></span>
			<span class="entry__share-action"><?php _e('Пошерить', 'knife-theme'); ?></span>
		</a>

		<a class="entry__share-item entry__share-item--vk" href="http://vk.com/share.php?url=<?php the_permalink(); ?>" target="_blank">
			<span class="icon icon--vk"></span>
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


if(!function_exists('knife_theme_entry_header')) :
/**
 * Prints entry author, category and date
 *
 * @since 1.1
 */

function knife_theme_entry_header() {
    knife_theme_authors_links();

	printf(
		'<time class="entry__header-meta" datetime="%1$s">%2$s</time>',
		get_the_time('c'),
		get_the_time('d F Y')
	);

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
 * @since 1.1
 */

function knife_theme_entry_related() {
	global $post;

	$cats = get_the_category();

	$entry_related = new WP_Query([
		'category__in' => $cats[0]->cat_ID,
		'post__not_in' => [$post->ID],
		'posts_per_page' => 6,
 		'ignore_sticky_posts' => 1,
 		'post_status' => 'publish'
	]);

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


if(!function_exists('knife_theme_social')) :
/**
 * Prints links to social community
 *
 * TODO: We have to rework this behaviour using menu or something better
 *
 * @since 1.1
 */

function knife_theme_social() {
?>

<ul class="social">
	<li class="social__item">
		<a class="social__item-link social__item-link--fb" href="https://www.facebook.com/theknifemedia">
			<span class="icon icon--fb"></span>
		</a>
	</li>

	<li class="social__item">
		<a class="social__item-link social__item-link--vk" href="https://vk.com/knife.media">
			<span class="icon icon--vk"></span>
		</a>
	</li>

	<li class="social__item">
		<a class="social__item-link social__item-link--telegram" href="http://telegram.me/knifemedia">
			<span class="icon icon--telegram"></span>
		</a>
	</li>

	<li class="social__item">
		<a class="social__item-link social__item-link--twitter" href="https://twitter.com/knife_media">
			<span class="icon icon--twitter"></span>
		</a>
	</li>
</ul>

<?php
}

endif;
