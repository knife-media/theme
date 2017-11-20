<?php
/**
 * Unit template using for news
 *
 * Prints publih time and all entry tags
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="unit unit--row">
	<?php echo knife_theme_category_link('unit__head') ?>

	<a class="unit__link" href="<?php the_permalink(); ?>">
		<div class="unit__image">
			<?php the_post_thumbnail('', ['class' => 'unit__image-thumbnail']); ?>
		</div>

		<footer class="unit__footer">
			<?php the_title('<p class="unit__title">', '</p>'); ?>

			<div class="unit__meta">
				<span class="unit__meta-item"><?php the_time(); ?></span>
 				<time class="unit__meta-item" datetime="<?php the_time(DATE_W3C); ?>"><?php echo get_the_date(); ?></time>

				<?php if(get_the_tags()) : ?>
					<span class="unit__meta-item"><?php knife_theme_tags(); ?></span>
				<?php endif; ?>
			</div>
		</footer>
	</a>
</article>
