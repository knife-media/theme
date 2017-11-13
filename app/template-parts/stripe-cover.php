<?php
/**
 * Stripe template with cover
 *
 * Stripe is a single content item using in any loop.
 * Can be 1/3, 1/2 or full screen width
 *
 * @package knife-theme
 * @since 1.1
 */
$category = get_the_category(); ?>

<article class="stripe__item stripe__item--cover">
	<?php if(isset($category[0])) : ?>
	<a class="stripe__item-head" href="<?php echo get_category_link($category[0]->term_id); ?>"><?php echo $category[0]->cat_name; ?></a>
	<?php endif; ?>

	<a class="stripe__item-link" href="<?php the_permalink(); ?>">

		<div class="stripe__item-image">
			<?php the_post_thumbnail('', ['class' => 'stripe__item-thumbnail']); ?>
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
