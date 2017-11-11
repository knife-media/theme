<?php
/**
 * Show last news in sidebar
 *
 * @package knife-theme
 * @since 1.1
 */

$include = get_category_by_slug('news');
$terms = get_the_tags(); ?>

<?php
	$news = new WP_Query([
		'category__in' => [$include->term_id],
		'post__not_in' => [$post->ID],
		'posts_per_page' => 8,
 		'post_status' => 'publish'
	]);


	if($news->have_posts()) :
?>

	<div class="sidebar__news">

<?php while($news->have_posts()) : $news->the_post(); ?>
		<div class="sidebar__news-item">

			<div class="sidebar__news-header">
				<time class="sidebar__news-meta" datetime="<?php the_time('c'); ?>">
					<?php the_time('d F Y');?>
				</time>

				<a class="sidebar__news-meta" href="<?php echo get_tag_link($terms[0]->term_id); ?>">
					<?php echo $terms[0]->name; ?>
				</a>
			</div>

			<a class="sidebar__news-link" href="<?php the_permalink();?>"><?php the_title(); ?></a>

		</div>

<?php endwhile; wp_reset_postdata(); ?>

	</div>

<?php endif; ?>
