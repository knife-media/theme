<?php
/**
 * Space stripe template
 *
 * Space is a transparent stripe with optional sticker
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="space__item">
	<a class="space__link" href="<?php the_permalink(); ?>">

<?php // TODO: rework ?>
		<?php if(!empty(get_post_meta($post->ID, 'post_icon', true))) : ?>
		<img class="space__sticker" src="<?php echo esc_url(get_post_meta($post->ID, 'post_icon', true)); ?>"/>
		<?php endif; ?>

		<footer class="space__footer">
			<div class="space__meta">
				<span class="space__meta-item"><?php knife_theme_authors(); ?></span>

				<time class="space__meta-item" datetime="<?php the_time(DATE_W3C); ?>"><?php echo get_the_date(); ?></time>
			</div>

			<?php the_title('<p class="space__title">', '</p>'); ?>
		</footer>
	</a>
</article>
