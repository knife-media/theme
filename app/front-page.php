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
	<div class="content__archive block">

<?php
	$q = new WP_Query(['posts_per_page' => 3, 'ignore_sticky_posts' => 1, 'post_status' => 'publish']);


	if ( $q->have_posts() ) :

		while ($q->have_posts()) : $q->the_post();
			knife_theme_unit('triple');
		endwhile;

		wp_reset_query();

	endif;
?>

 <?php
	$q = new WP_Query(['posts_per_page' => 1, 'ignore_sticky_posts' => 1, 'post_status' => 'publish', 'offset' => 3]);


	if ( $q->have_posts() ) :

		while ($q->have_posts()) : $q->the_post();
			knife_theme_unit('single');
		endwhile;

		wp_reset_query();

	endif;
?>

<section class="mindmap">
<?php
	$q = new WP_Query(['posts_per_page' => 8, 'ignore_sticky_posts' => 1, 'post_status' => 'publish', 'offset' => 9]);


	if ( $q->have_posts() ) :

		while ($q->have_posts()) : $q->the_post();
			get_template_part('template-parts/mindmap');
   	endwhile;

		wp_reset_query();

	endif;
?>
</section>


<section class="spacing">
<?php
	$q = new WP_Query(['posts_per_page' => 4, 'ignore_sticky_posts' => 1, 'post_status' => 'publish', 'offset' => 10]);


	if ( $q->have_posts() ) :

		while ($q->have_posts()) : $q->the_post();
?>
	<article class="spacing__item">
<?php
	if($q->current_post === 0)
		echo knife_theme_category_link('spacing__item-head');
?>

	<a class="spacing__item-link" href="<?php the_permalink(); ?>">

		<?php // TODO: rework ?>
		<?php if(!empty(get_post_meta($post->ID, 'post_icon', true))) : ?>
		<img class="spacing__item-sticker" src="<?php echo esc_url(get_post_meta($post->ID, 'post_icon', true)); ?>"/>
		<?php endif; ?>

		<footer class="spacing__item-footer">
			<div class="spacing__item-info">
				<span class="spacing__item-meta"><?php knife_theme_authors() ?></span>

				<time class="spacing__item-meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F');?></time>
			</div>

			<p class="spacing__item-title"><?php the_title(); ?></p>
		</footer>

	</a>
</article>
<?php
   	endwhile;

		wp_reset_query();

	endif;
?>
</section>

	</div>
</main>

<?php

get_footer();
