<?php
/**
 * Widget template using for news
 *
 * Prints publih time and all entry tags
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="widget__item">
	<a class="widget__link" href="<?php the_permalink(); ?>">
		<div class="widget__image">
			<?php the_post_thumbnail('thumbnail', ['class' => 'widget__image-thumbnail']); ?>
		</div>

		<footer class="widget__footer">
			<?php
				the_title('<p class="widget__title">', '</p>');

				knife_theme_meta([
					'items' => ['time', 'date', 'tags'],
					'before' => '<div class="widget__meta meta">',
					'after' => '</div>',
					'is_link' => false
				]);
			?>
		</footer>
	</a>
</article>
