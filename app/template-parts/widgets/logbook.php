<?php
/**
 * widget template using for news
 *
 * Prints publih time and all entry tags
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="<?php knife_theme_widget_args('widget widget--logbook', $widget_settings ?? []); ?>">
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
				/*
			?>

			<div class="widget__meta">
				<span class="widget__meta-item"><?php the_time(); ?></span>
 				<time class="widget__meta-item" datetime="<?php the_time(DATE_W3C); ?>"><?php echo get_the_date(); ?></time>

				<?php if(get_the_tags()) : ?>
					<span class="widget__meta-item"><?php knife_theme_tags(); ?></span>
				<?php endif; ?>
			</div>
				 */ ?>
		</footer>
	</a>
</article>
