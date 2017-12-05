<?php
/**
 * Stripe widget template
 *
 * @package knife-theme
 * @since 1.1
 */
?>


<article class="<?php knife_theme_widget_options('widget__item'); ?>">
	<?php
		knife_theme_meta([
			'items' => ['category'],
			'item_before' => '',
			'item_after' => '',
			'link_class' => 'widget__head'
		]);
	?>

	<a class="widget__link" href="<?php the_permalink(); ?>">
		<div class="widget__image">
			<?php
				the_post_thumbnail(get_query_var('widget_image', 'triple'), ['class' => 'widget__image-thumbnail']);
			?>
		</div>

		<footer class="widget__footer">
			<?php
				the_title('<p class="widget__title">', '</p>');

				knife_theme_meta([
					'items' => ['author', 'date'],
					'before' => '<div class="widget__meta meta">',
					'after' => '</div>',
					'is_link' => false
				]);
			?>
		</footer>
	</a>
</article>
