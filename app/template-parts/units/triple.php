<?php
/**
 * Unit template with 1/3 screen width
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="unit unit--triple">
	<?php
		knife_theme_meta([
			'items' => ['category'],
			'link_class' => 'unit__head'
		]);
	?>

	<a class="unit__link" href="<?php the_permalink(); ?>">
		<div class="unit__image">
			<?php the_post_thumbnail('triple', ['class' => 'unit__image-thumbnail']); ?>
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
