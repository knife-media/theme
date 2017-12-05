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
 * Prints post meta info
 *
 * Post date, authors, category and optional tags
 *
 * @since 1.1
 */
function knife_custom_background() {
	if(!is_front_page())
		return;

    $background = set_url_scheme(get_background_image());

    $color = get_background_color();

    if($color === get_theme_support('custom-background', 'default-color'))
        $color = false;

    if (!$background && !$color) {
        if(!is_customize_preview())
			return;

        $html = '<style type="text/css" id="custom-background-css"></style>';
    }

    $style = $color ? "background-color: #$color;" : '';

    if ($background) :
        $image = ' background-image: url("' . esc_url_raw( $background ) . '");';

        $position_x = get_theme_mod('background_position_x', get_theme_support('custom-background', 'default-position-x'));
        $position_y = get_theme_mod('background_position_y', get_theme_support('custom-background', 'default-position-y'));

        if(!in_array($position_x, ['left', 'center', 'right'], true))
            $position_x = 'left';

        if(!in_array($position_y, ['top', 'center', 'bottom'], true))
            $position_y = 'top';

        $position = " background-position: $position_x $position_y;";

        $size = get_theme_mod('background_size', get_theme_support('custom-background', 'default-size'));

        if(!in_array($size, ['auto', 'contain', 'cover'], true))
            $size = 'auto';

        $size = " background-size: $size;";

        $repeat = get_theme_mod('background_repeat', get_theme_support('custom-background', 'default-repeat'));

        if(!in_array($repeat, ['repeat-x', 'repeat-y', 'repeat', 'no-repeat'], true))
            $repeat = 'repeat';

        $repeat = " background-repeat: $repeat;";

        $attachment = get_theme_mod('background_attachment', get_theme_support('custom-background', 'default-attachment'));

        if('fixed' !== $attachment)
            $attachment = 'scroll';

        $attachment = " background-attachment: $attachment;";

        $html = $style . $image . $position . $size . $repeat . $attachment;
	endif;

	$html = apply_filters('knife_custom_background', $html);

	echo '<style type="text/css" id="custom-background-css">.wrap{' . $html . '}</style>';
}

endif;


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
		'items' => ['author', 'date', 'category'],
		'before' => '',
		'after' => '',
		'item_before' => '<span class="meta__item">',
		'item_after' => '</span>',
		'link_class' => 'meta__link',
		'is_link' => true,
		'echo' => true
	];

	$args = wp_parse_args($args, $defaults);

	foreach($args['items'] as $item) {
		$html .= $args['item_before'];

		switch($item) {
			case 'author':

				if($args['is_link'] === true) :

					if(function_exists('coauthors_posts_links'))
						$html .= coauthors_posts_links(null, null, null, null, false);
					else
						$html .= get_the_author_posts_link();

				else :

 					if(function_exists('coauthors'))
						$html .= coauthors(null, null, null, null, false);
					else
						$html .= get_the_author();

				endif;

				break;

			case 'category':

				$cats = get_the_category();

				if(!isset($cats[0]))
					break;

				if($args['is_link'] === true) :

					$html .= sprintf(
						'<a class="%2$s" href="%1$s">%3$s</a>',
						esc_url(get_category_link($cats[0]->term_id)),
						esc_attr($args['link_class']),
						sanitize_text_field($cats[0]->cat_name)
					);

				else :

					$html .= sanitize_text_field($cats[0]->cat_name);

				endif;

				break;

			case 'date':

 				$html .= sprintf(
					'<time datetime="%1$s">%2$s</time>',
					get_the_time('c'),
					get_the_time('j F Y')
				);

				break;

			case 'time':

 				$html .= get_the_time();

				break;

			case 'tag' :

				if($args['is_link'] === true) :

					$html .= knife_theme_tags([
						'item' => '<a class="' . esc_attr($args['link_class']) . '" href="%2$s">%1$s</a>',
						'count' => 1,
						'echo' => false
					]);

				else:

					$html .= knife_theme_tags([
						'item' => '%1$s',
						'count' => 1,
						'echo' => false
					]);

				endif;

				break;

			case 'tags' :

				if($args['is_link'] === true) :

					$html .= knife_theme_tags([
						'item' => '<a class="' . esc_attr($args['link_class']) . '" href="%2$s">%1$s</a>',
						'count' => 3,
						'echo' => false,
						'between' => ', '
					]);

				else:

					$html .= knife_theme_tags([
						'item' => '%1$s',
						'count' => 3,
						'echo' => false,
 						'between' => ', '
					]);

				endif;

				break;
		}

		$html .= $args['item_after'];
	}

	$html = $args['before'] . $html . $args['after'];

  	// Filter result html before return
	$html = apply_filters('knife_theme_meta', $html, $args);

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

  	// Filter result html before return
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

		$html .= sprintf($args['item'], sanitize_text_field($tag->name), get_tag_link($tag->term_id));
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
		$html .= sprintf($args['item'], get_the_title($item->ID), get_the_permalink($item->ID));
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
		'item' => '<a class="share__link share__link--%3$s" href="%1$s" target="_blank" data-id="%3$s">%2$s</a>',
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
			get_the_title()
		);

		$html .= sprintf($args['item'],
			esc_url($item_link),
			$item_icon . $item_text,
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
 * Prints single post meta by post ID
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

    if(get_query_var('widget_cover', 'default') === 'cover')
		$options[] = $base . '--cover';

    elseif(get_query_var('widget_cover', 'default') === 'nocover')
		$tt = '';
	elseif($post_id > 1 && $cover = get_post_meta($post_id, '_knife-theme-cover', true))
		$options[] = $base . '--cover';

	$html = join(' ', $options);

	// Filter result html before return
	$html = apply_filters('knife_theme_widget_options', $html);

	echo $html;
}

endif;


if(!function_exists('knife_theme_widget_template')) :
/**
 * Helper function to wrap stripe template part with custom parent tag
 *
 * @since 1.1
 */
function knife_theme_widget_template($args) {
	global $wp_query;

	$defaults = [
		'template' => 'template-parts/widgets/stripe',
		'size' => 'triple',
		'before' => '<div class="widget">',
		'after' => '</div>'
	];

	$args = wp_parse_args($args, $defaults);

	if($wp_query->current_post % 5 === 3 || $wp_query->current_post % 5 === 4)
		$args['size'] = 'double';

	printf($args['before'], esc_attr($args['size']));

	set_query_var('widget_image', $args['size']);
	get_template_part($args['template']);

	echo $args['after'];
}

endif;
