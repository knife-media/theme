<?php
/**
 * Template for showing site front-page
 *
 * Difference between front-page and home.php in a link below
 *
 * @link https://wordpress.stackexchange.com/a/110987
 *
 * @package knife-theme
 * @since 1.1
 */

get_header();

?>

<main <?php knife_theme_cover("content", $post->ID); ?>>
	<div class="content__inner container">

<?php
	$news = get_category_by_slug('news');

	$sidebar_related = new WP_Query([
		'category__in' => 14,
		'post__not_in' => [$post->ID],
		'posts_per_page' => 4,
		'offset' => 0,
		'ignore_sticky_posts' => 1,
		'post_status' => 'publish'
	]);

	if($sidebar_related->have_posts()) :
?>
   <section class="stripe stripe--triple">

<?php
		while($sidebar_related->have_posts()) :
			$sidebar_related->the_post();

			get_template_part('template-parts/stripe', 'cover');
		endwhile;

		wp_reset_postdata();
?>

	</section>

  <section class="stripe stripe--spacing">

<?php
		while($sidebar_related->have_posts()) :
			$sidebar_related->the_post();

			get_template_part('template-parts/stripe', 'spacing');
		endwhile;

		wp_reset_postdata();
?>

	</section>

   <section class="stripe stripe--single">

<?php
		while($sidebar_related->have_posts()) :
			$sidebar_related->the_post();

			get_template_part('template-parts/stripe', 'cover');
		endwhile;

		wp_reset_postdata();
?>

	</section>

<?php endif; ?>

</main>

<?php

get_footer();
