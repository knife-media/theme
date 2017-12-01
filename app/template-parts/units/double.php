<?php
/**
 * Unit template with 1/2 screen width
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="unit unit--double">
	<?php
		knife_theme_meta([
			'items' => ['category'],
			'before' => '<div class="unit__head meta">',
			'after' => '</div>'
		]);
	?>

	<a class="unit__link" href="<?php the_permalink(); ?>">
		<div class="unit__image">
			<?php the_post_thumbnail('double-thumbnail', ['class' => 'unit__image-thumbnail']); ?>
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
