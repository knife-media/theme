<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */



if(!function_exists('knife_theme_meta')) :
/**
 * Prints post meta info
 *
 * Post date, authors, category and optional tags
 *
 * @since 1.1
 */
function knife_theme_meta($args, $html = '') {
	$defaults = [
		'opts' => ['author', 'date', 'category'],
		'before' => '',
		'after' => '',
		'item' => '<span class="meta__item">%s</span>',
		'link' => '<a class="meta__link" href="%2$s">%1$s</a>',
		'is_link' => true,
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	foreach($args['opts'] as $item) {
		switch($item) {

			// Prints post author
			case 'author':
				if($args['is_link'] === false) :
					if(function_exists('coauthors'))
						$html .= sprintf($args['item'], coauthors('', '', null, null, false));
					else
						$html .= sprintf($args['item'], get_the_author());
				else :
					if(function_exists('coauthors_posts_links'))
						$html .= coauthors_posts_links('', '', null, null, false);
					else
						$html .= get_the_author_posts_link();
				endif;
			break;

			// Prints post category
			case 'category':
				$cats = get_the_category();

				if(!isset($cats[0]))
					break;

				if($args['is_link'] === false) :
					$html .= sprintf($args['item'], sanitize_text_field($cats[0]->cat_name));
				else :
					$html .= sprintf($args['link'],
						sanitize_text_field($cats[0]->cat_name),
						esc_url(get_category_link($cats[0]->term_id))
					);
				endif;
			break;

			// Prints post publish date. Exclude current year
			case 'date':
				$date = sprintf('<time datetime="%1$s">%2$s</time>',
					get_the_time('c'),
					get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
				);

 				$html .= sprintf($args['item'], $date);
			break;

			// Prints only post publish time. Useful for news
			case 'time':
 				$html .= sprintf($args['item'], get_the_time('H:i'));
			break;

			//  Prints post single tag
			case 'tag' :
				if($args['is_link'] === false) :
					$html .= knife_theme_tags(['item' => '%1$s', 'count' => 1, 'echo' => false]);
				else :
					$html .= knife_theme_tags(['item' => $args['link'], 'count' => 1, 'echo' => false]);
				endif;
			break;

			// Same as above but for 3 tags
			case 'tags' :
				if($args['is_link'] === false) :
					$html .= knife_theme_tags(['item' => '%1$s', 'count' => 3, 'echo' => false, 'between' => '']);
				else :
					$html .= knife_theme_tags(['item' => $args['link'], 'count' => 3, 'echo' => false, 'between' => '']);
				endif;
			break;

			// Show widget head using widget query vars
			case 'head' :
				$title = get_query_var('widget_title', '');
 				$link = get_query_var('widget_link', '');

				if(empty($title))
					break;

				if(empty($link)) :
					$html .= sprintf($args['item'], sanitize_text_field($title));
				else:
					$html .= sprintf($args['link'], sanitize_text_field($title), esc_url($link));
				endif;
			break;
		}
	}

	$html = $args['before'] . $html . $args['after'];

  	// Filter result html before return
	$html = apply_filters('knife_theme_meta', $html, $args);

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
function knife_theme_tags($args, $html = '') {
 	$defaults = [
		'before' => '',
		'after' => '',
		'item' => '%1$s',
		'between' => '',
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

		if($i > 0)
			$html .= $args['between'];

		$html .= sprintf($args['item'], $tag->name, get_tag_link($tag->term_id));
	}


	$html = $args['before'] . $html . $args['after'];

 	// Filter result html before return
	$html = apply_filters('knife_theme_tags', $html, $args);

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
function knife_theme_related($args, $html = '') {
	$defaults = [
		'before' => '',
		'after' => '',
		'title' => '<div class="refers__title">%s</div>',
		'item' => '<div class="refers__item"><a class="refers__link" href="%2$s">%1$s</a></div>',
		'echo' => true
	];

	global $post;

	$args = wp_parse_args($args, $defaults);
	$cats = get_the_category();

	if(!isset($cats[0]) || !isset($post->ID))
		return false;

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
		$html .= sprintf($args['item'],
			get_the_title($item->ID),
			get_the_permalink($item->ID)
		);
	}

	$html = $args['before'] . $title . $html . $args['after'];

	// Filter result html before return
 	$html = apply_filters('knife_theme_related', $html, $args);

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
function knife_theme_share($args, $html = '') {
	$defaults = [
		'before' => '',
		'after' => '',
		'title' => '<div class="share__title">%s</div>',
		'text' => '<span class="share__text">%s</span>',
		'item' => '<a class="share__link share__link--%3$s" href="%2$s" target="_blank" data-id="%3$s">%1$s</a>',
		'icon' => '<span class="icon icon--%s"></span>',
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	$title = sprintf($args['title'], __('Поделиться в соцсетях:'));

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

		$item_link = sprintf($data['link'],
			get_permalink(),
			strip_tags(get_the_title())
		);

		$html .= sprintf($args['item'],
			$item_icon . $item_text,
			esc_url($item_link),
			esc_attr($network)
		);
	}

	$html = $args['before'] . $title . $html . $args['after'];

  	// Filter result html before return
	$html = apply_filters('knife_theme_share', $html, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_post_meta')) :
/**
 * Prints single post meta with before and after tags by post ID
 *
 * @since 1.1
 */
function knife_theme_post_meta($args, $post_id = null) {
	global $post;

	$defaults = [
		'before' => '',
		'after' => '',
		'meta' => '',
		'item' => '%s',
		'post_id' => $post_id ?? $post->ID,
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	$meta = get_post_meta($args['post_id'], $args['meta'], true);

	if(empty($meta))
		return false;

	$item = sprintf($args['item'], $meta);

	$html = $args['before'] . $item . $args['after'];

  	// Filter result html before return
	$html = apply_filters('knife_theme_post_meta', $html, $args);

	if($args['echo'] === false)
		return $html;

	echo $html;
}

endif;


if(!function_exists('knife_theme_widget_options')) :
/**
 * Merge and prints widget options as classes
 *
 * @since 1.1
 */
function knife_theme_widget_options($base = 'widget__item', $post_id = null) {
	global $post;

	$post_id = $post_id ?? $post->ID;
	$options = [$base];

	switch(get_query_var('widget_cover', 'default')) {
		case 'cover':
			$options[] = $base . '--cover';

			break;

		case 'nocover':
			break;

		default:
			if(!get_post_meta($post_id, '_knife-theme-cover', true))
				break;

			$options[] = $base . '--cover';
	}

	$html = join(' ', $options);

	// Filter result html before return
	$html = apply_filters('knife_theme_widget_options', $html);

	echo $html;
}

endif;


if(!function_exists('knife_theme_widget_template')) :
/**
 * Helper function to wrap template part with custom parent tag
 *
 * @since 1.1
 */
function knife_theme_widget_template($args = []) {
	global $wp_query;

	$defaults = [
		'size' => null,
		'before' => '<div class="widget widget-%s">',
		'after' => '</div>'
	];

	$args = wp_parse_args($args, $defaults);

	$opts = function($current, $found) use (&$args) {
		if($found < 3 || $current % 5 === 3 || $current % 5 === 4)
			return 'double';

		return 'triple';
	};


	if($args['size'] === null)
		$args['size'] = $opts($wp_query->current_post, (int) $wp_query->found_posts);


	printf($args['before'], esc_attr($args['size']));

	get_template_part('template-parts/widgets/' . $args['size']);

	echo $args['after'];
}

endif;


if(!function_exists('knife_custom_background')) :
/**
 * Custom background callback
 *
 * We use it instead of default callback to update body to .wrap
 *
 * @link https://developer.wordpress.org/reference/functions/_custom_background_cb/
 * @since 1.1
 */
function knife_custom_background($style = []) {
	$image = set_url_scheme(get_background_image());
	$color = get_background_color();

	if($color === get_theme_support('custom-background', 'default-color'))
		$color = false;

	if($color)
		$style[] = "background-color: #" . $color . ";";

	if($image)
		$style[] = "background-image: url(" . esc_url($image) . ");";

	$html = sprintf(
		'<div class="backdrop" style="%s"></div>',
		implode(' ', $style)
	);

 	// Filter result html before return
	$html = apply_filters('knife_custom_background', $html);

	echo $html;
}

endif;
