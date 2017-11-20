<?php
/**
 * Sidebar related posts
 *
 * @package knife-theme
 * @since 1.1
 */

$news = get_category_by_slug('news'); ?>

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
