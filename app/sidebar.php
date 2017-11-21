<?php
/**
 * Single template sidebar
 *
 * Show last news and related posts
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<aside class="sidebar">
<?php
	$news = get_category_by_slug('news');
	$sidebar_news = new WP_Query([
		'category__in' => [$news->term_id],
		'post__not_in' => [$post->ID],
		'posts_per_page' => 8,
 		'ignore_sticky_posts' => 1,
 		'post_status' => 'publish'
	]);


	if($sidebar_news->have_posts()) :
?>

	<div class="sidebar__news">

<?php while($sidebar_news->have_posts()) : $sidebar_news->the_post(); ?>
		<div class="sidebar__news-item">

			<div class="sidebar__news-header">
				<time class="sidebar__news-meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F Y');?></time>

				<?php $tags = get_the_tags(); ?>

				<?php if(isset($tags[0])) : ?>
				<a class="sidebar__news-meta" href="<?php echo get_tag_link($tags[0]->term_id); ?>"><?php echo $tags[0]->name; ?></a>
				<?php endif; ?>
			</div>

			<a class="sidebar__news-link" href="<?php the_permalink();?>"><?php the_title(); ?></a>
	  </div>
<?php endwhile; wp_reset_postdata(); ?>

	</div>

<?php endif; ?>

<?php
	$sidebar_related = new WP_Query([
		'category__not_in' => [$news->term_id],
		'post__not_in' => [$post->ID],
		'posts_per_page' => 4,
		'ignore_sticky_posts' => 1,
		'post_status' => 'publish'
	]);

	if($sidebar_related->have_posts()) :
?>

	<div class="sidebar__related">

<?php while($sidebar_related->have_posts()) : $sidebar_related->the_post(); ?>
		<a class="sidebar__related-item" href="<?php the_permalink(); ?>">

			<?php the_post_thumbnail('cb-360-240', ['class' => 'sidebar__related-image']); ?>
			<p class="sidebar__related-title"><?php the_title(); ?></p>

		</a>
<?php endwhile; wp_reset_postdata(); ?>

	</div>

<?php endif; ?>
</aside>
