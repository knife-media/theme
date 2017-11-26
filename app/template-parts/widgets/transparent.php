<?php
/**
 * Transparent widget template
 *
 * Transparent is a transparent stripe with optional sticker
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="widget__item">
	<a class="widget__link" href="<?php the_permalink(); ?>">

<?php // TODO: rework ?>
		<?php if(!empty(get_post_meta($post->ID, 'post-sticker', true))) : ?>
		<img class="widget__sticker" src="<?php echo esc_url(get_post_meta($post->ID, 'post-sticker', true)); ?>"/>
		<?php endif; ?>

		<footer class="widget__footer">
			<div class="widget__meta">
				<span class="widget__meta-item"><?php knife_theme_authors(); ?></span>

				<time class="widget__meta-item" datetime="<?php the_time(DATE_W3C); ?>"><?php echo get_the_date(); ?></time>
			</div>

			<?php the_title('<p class="widget__title">', '</p>'); ?>
		</footer>
	</a>
</article>
