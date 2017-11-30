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
		'item_before' => '<span class="meta__item">',
		'item_after' => '</span>',
		'link_class' => 'meta__link',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	foreach($args['items'] as $item) {
		$meta .= $args['item_before'];

		switch($item) {
			case 'author':

				if(function_exists('coauthors_posts_links'))
					$meta .= coauthors_posts_links(null, null, null, null, false);
				else
					$meta .= get_the_author_posts_link();

				break;

			case 'category':

				$cats = get_the_category();

				if(!isset($cats[0]))
					break;

				$meta .= sprintf(
					'<a class="%2$s" href="%1$s">%3$s</a>',
					esc_url(get_category_link($cats[0]->term_id)),
					esc_attr($args['link_class']),
					sanitize_text_field($cats[0]->cat_name)
				);

				break;

			case 'date':

 				$meta .= sprintf(
					'<time datetime="%1$s">%2$s</time>',
					get_the_time('c'),
					get_the_time('d F Y')
				);

				break;
		}

		$meta .= $args['item_after'];
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
		'count' => 100,
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
		'item' => '<p><a href="%1$s">%2$s</p>',
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
		$list .= sprintf($args['item'], get_the_title($item->ID), get_the_permalink($item->ID));
	}

	$list = $args['before'] . $title . $list . $args['after'];

 	$html = apply_filters('knife_theme_related', $list, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_share')) :
/**
 * Prints social share buttons template
 *
 * @since 1.1
 */

function knife_theme_share($args, $list = '') {
	$defaults = [
		'before' => '',
		'after' => '',
		'title' => '<div class="share__title">%s</div>',
		'text' => '<span class="share__text">%s</span>',
		'item' => '<a class="share__link share__link--%3$s" href="%1$s" target="_blank">%2$s</a>',
		'icon' => '<span class="icon icon--%s"></span>',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	$title = sprintf($args['title'], __('Поделиться в соцсетях: '));

	$share_links = [
		'facebook' => [
			'link' => 'http://www.facebook.com/sharer/sharer.php?p[url]=%1$s&p[title]=%2$s',
			'text' => __('Пошерить', 'knife-theme')
		],

		'vkontakte' => [
			'link' => 'http://vk.com/share.php?url=%1$s&text=%2$s',
			'text' => __('Поделиться', 'knife-theme')
		],

		'telegram' => [
			'link' => 'https://t.me/share/url?url=%1$s&text=%2$s',
			'text' => null
		],

		'twitter' => [
			'link' => 'https://twitter.com/intent/tweet?text=%2$s&url=%1$s',
			'text' => null
		]
	];

	$share_links = apply_filters('knife_theme_share_links', $share_links);

	foreach($share_links as $network => $data) {
		$item_text = !empty($data['text']) ? sprintf($args['text'], $data['text']) : '';
		$item_icon = sprintf($args['icon'], $network);

		$item_link = sprintf($data['text'],
			get_permalink(),
			get_the_title()
		);

		$list .= sprintf($args['item'],
			esc_url($item_link),
			$item_icon . $item_text,
			esc_attr($network)
		);
	}

	$list = $args['before'] . $title . $list . $args['after'];

	$html = apply_filters('knife_theme_share', $list, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;




