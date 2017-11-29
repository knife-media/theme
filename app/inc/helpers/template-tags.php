<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */


function knife_custom_background() {}


if(!function_exists('knife_theme_meta')) :
/**
 * Prints post meta info
 *
 * Shows post date, authors, category and optional tags
 *
 * @since 1.1
 */

function knife_theme_meta($args, $meta = '') {
	$defaults = [
		'items' => ['author', 'date', 'category'],
		'before' => '',
		'after' => '',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	foreach($args['items'] as $item) {
		switch($item) {
			case 'author':

				if(function_exists('coauthors_posts_links'))
					$meta .= coauthors_posts_links('', '', null, null, false);
				else
					$meta .= get_the_author_posts_link();

				break;

			case 'category':

				$cats = get_the_category();

				if(!isset($cats[0]))
					break;

				$meta .= sprintf(
					'<a class="meta__item" href="%1$s">%2$s</a>',
					esc_url(get_category_link($cats[0]->term_id)),
					sanitize_text_field($cats[0]->cat_name)
				);

				break;

			case 'date':
 				$meta .= sprintf(
					'<time class="meta__item" datetime="%1$s">%2$s</time>',
					get_the_time('c'),
					get_the_time('d F Y')
				);

				break;
		}
	}

	$meta = $args['before'] . $meta . $args['after'];

	$html = apply_filters('knife_theme_meta', $meta, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_excerpt')) :
/**
 * Displays the optional excerpt
 *
 * Wraps the excerpt in a div element
 *
 * @since 1.1
 */

function knife_theme_excerpt($args) {
  	$defaults = [
		'before' => '',
		'after' => '',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	if(!has_excerpt())
		return false;

	$html = $args['before'] . apply_filters('the_excerpt', get_the_excerpt()) . $args['after'];

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_tags')) :
/**
 * Prints current post tags list
 *
 * @since 1.1
 */
function knife_theme_tags($args, $list = '') {
 	$defaults = [
		'before' => '',
		'after' => '',
		'item' => '%1$s',
		'between' => ', ',
		'count' => 99,
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);
	$tags = get_the_tags();

	if($tags === false)
		return false;

	foreach($tags as $i => $tag) {
		if($args['count'] <= $i)
			continue;

		$list .= sprintf($args['item'], sanitize_text_field($tag->name), get_tag_link($tag->term_id));

		if(count($tags) > $i + 1)
			$list .= $args['between'];
	}


	$list = $args['before'] . $list . $args['after'];

	$html = apply_filters('knife_theme_tags', $list, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_related')) :
/**
 * Prints related posts by category
 *
 * @since 1.1
 */

function knife_theme_related($args, $list = '') {
	$defaults = [
		'before' => '',
		'after' => '',
		'title' => '',
		'item' => '<p>< href="%1$s">%2$s</p>',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);
	$cats = get_the_category();

	if(!isset($cats[0]))
		return false;

	global $post;

	$title = sprintf($args['title'], __('Читайте также:', 'knife-theme'));

	$items = get_posts([
 		'post__not_in' => [$post->ID],
		'posts_per_page' => 6,
 		'ignore_sticky_posts' => 1,
 		'post_status' => 'publish',
		'category__in' => $cats[0]->cat_ID
	]);

	if(count($items) < 1)
		return false;

	foreach($items as $item) {
		$list .= sprintf($args['item'], get_the_permalink($item->ID), get_the_title($item->ID));
	}

	$list = $args['before'] . $title . $list . $args['after'];

 	$html = apply_filters('knife_theme_related', $list, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
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




