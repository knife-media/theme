<?php
/**
 * Feature widget template
 *
 * Feature is an important single post with feature meta
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<a class="widget__link" href="<?php echo get_query_var('widget_link', get_permalink()); ?>">
	<div class="widget__item block">
		<?php
			printf(
				'<p class="widget__title">%s</p>',
				get_query_var('widget_head', get_the_title())
			);

			knife_theme_post_meta([
				'item' => '<img class="widget__sticker" src="%s">',
				'meta' => 'post-sticker',
				'post_id' => get_query_var('widget_base', get_the_ID())
			]);
		?>

		<span class="icon icon--right"></span>
	</div>
</a>
