<?php
/**
 * Spacing is a transparent stripe with optional sticker
 *
 * Stripe is a single content item using in any loop.
 * Can be 1/3, 1/2 or full screen width
 *
 * @package knife-theme
 * @since 1.1
 */
$category = get_the_category();
?>



<article class="spacing__item">
	<a class="spacing__link" href="<?php the_permalink(); ?>">

<?php // TODO: rework ?>
		<?php if(!empty(get_post_meta($post->ID, 'post_icon', true))) : ?>
		<img class="spacing__sticker" src="<?php echo esc_url(get_post_meta($post->ID, 'post_icon', true)); ?>"/>
		<?php endif; ?>

		<footer class="spacing__footer">
			<div class="spacing__meta">
				<span class="spacing__meta-item"><?php knife_theme_authors(); ?></span>

				<time class="spacing__meta-item" datetime="<?php the_time(DATE_W3C); ?>"><?php echo get_the_date(); ?></time>
			</div>

			<?php the_title('<p class="spacing__title">', '</p>'); ?>
		</footer>
	</a>
</article>
