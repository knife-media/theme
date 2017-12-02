<?php
/**
 * Full width unit template
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="unit unit--single unit--cover">
	<?php
		knife_theme_meta([
			'items' => ['category'],
			'link_class' => 'unit__head'
		]);
	?>

	<a class="unit__link" href="<?php the_permalink(); ?>">
		<div class="unit__image">
			<?php the_post_thumbnail('single', ['class' => 'unit__image-thumbnail']); ?>
		</div>

		<footer class="unit__footer">
			<?php
				the_title('<p class="unit__title">', '</p>');

				knife_theme_meta([
					'items' => ['author', 'date'],
					'before' => '<div class="unit__meta meta">',
					'after' => '</div>',
					'is_link' => false
				]);
			?>
		</footer>
	</a>
</article>
