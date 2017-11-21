<?php
/**
 * Recent news widget template
 *
 * Typically used in sidebar to print last news
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="widget__item">

	<div class="widget__header">
		<time class="widget__meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F Y');?></time>

		<?php $tags = get_the_tags(); ?>

		<?php if(isset($tags[0])) : ?>
		<a class="widget__meta" href="<?php echo get_tag_link($tags[0]->term_id); ?>"><?php echo $tags[0]->name; ?></a>
		<?php endif; ?>
	</div>

	<a class="widget__link" href="<?php the_permalink();?>"><?php the_title(); ?></a>
</article>
