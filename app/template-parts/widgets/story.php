<?php
/**
 * Story widget template
 *
 * @package knife-theme
 * @since 1.3
 */
?>


<article class="<?php knife_theme_widget_options('widget__item'); ?>">
	<div class="widget__image">
		<?php
			the_post_thumbnail('triple', ['class' => 'widget__image-thumbnail']);
		?>
	</div>

	<footer class="widget__footer">
		<?php
			printf(
				'<a class="widget__link" href="%2$s">%1$s</a>',
				the_title('<p class="widget__title">', '</p>', false),
				get_permalink()
			);
		?>
	</footer>
</article>
