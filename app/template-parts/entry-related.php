<?php
/**
 * Entry related template
 *
 * @package knife-theme
 * @since 1.1
 */

$cats = get_the_category(); ?>

<?php
	$entry_related = new WP_Query([
		'category__in' => $cats[0]->cat_ID,
		'post__not_in' => [$post->ID],
		'posts_per_page' => 6,
 		'ignore_sticky_posts' => 1,
 		'post_status' => 'publish'
	]);


	if($entry_related->have_posts()) :
?>

	<div class="entry__related">
		<p class="entry__related-title"><?php _e('Читайте также:', 'knife-theme'); ?></p>

<?php while($entry_related->have_posts()) : $entry_related->the_post(); ?>
		<div class="entry__related-item">
			<a class="entry__related-link" href="<?php the_permalink();?>"><?php the_title(); ?></a>
		</div>
<?php endwhile; wp_reset_postdata(); ?>

	</div>

<?php endif; ?>
