<?php
/**
 * Double widget template
 *
 * @package knife-theme
 * @since 1.1
 */
?>


<article class="<?php knife_theme_widget_options('widget__item'); ?>">
	<?php
		knife_theme_meta([
			'opts' => ['tag'],
			'before' => '<div class="widget__head">',
			'after' => '</div>',
			'item' => '<span class="widget__head-item">%s</span>',
			'link' => '<a class="widget__head-link" href="%2$s">%1$s</a>'
		]);
	?>

	<div class="widget__image">
		<?php
			the_post_thumbnail('double', ['class' => 'widget__image-thumbnail']);
		?>
	</div>

	<footer class="widget__footer">
		<?php
			printf(
				'<a class="widget__link" href="%2$s">%1$s</a>',
				the_title('<p class="widget__title">', '</p>', false),
				get_permalink()
			);

			knife_theme_meta([
				'opts' => ['author', 'date'],
				'before' => '<div class="widget__meta meta">',
				'after' => '</div>'
			]);
		?>
	</footer>
</article>
