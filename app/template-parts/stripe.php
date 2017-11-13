<?php
/**
 * Double stripe template
 *
 * Stripe is a single content item using in any loop
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="stripe__item">
		<a class="stripe__item-link" href="<?php the_permalink(); ?>">

			<div class="stripe__item-image">
				<?php the_post_thumbnail('cb-360-240', ['class' => 'stripe__item-thumbnail']); ?>
			</div>

			<footer class="stripe__item-footer">
				<p class="stripe__item-title"><?php the_title(); ?></p>

				<div class="stripe__item-info">
    			<span class="stripe__item-meta"><?php knife_theme_authors() ?></span>

					<time class="stripe__item-meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F');?></time>
				</div>
			</footer>

		</a>

</article>
