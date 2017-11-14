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
$category = get_the_category(); ?>

<article class="stripe__item">
 	<?php if(isset($category[0])) : ?>
	<a class="stripe__item-head" href="<?php echo get_category_link($category[0]->term_id); ?>"><?php echo $category[0]->cat_name; ?></a>
	<?php endif; ?>

	<a class="stripe__item-link" href="<?php the_permalink(); ?>">

		<?php // TODO: rework ?>
		<?php if(!empty(get_post_meta($post->ID, 'post_icon', true))) : ?>
		<img class="stripe__item-sticker" src="<?php echo esc_url(get_post_meta($post->ID, 'post_icon', true)); ?>"/>
		<?php endif; ?>

		<footer class="stripe__item-footer">
			<div class="stripe__item-info">
				<span class="stripe__item-meta"><?php knife_theme_authors() ?></span>

				<time class="stripe__item-meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F');?></time>
			</div>

			<p class="stripe__item-title"><?php the_title(); ?></p>
		</footer>

	</a>
</article>
