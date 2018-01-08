<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

<?php if(have_posts()) : ?>
	<div class="caption block">
 		<h1 class="caption__title caption__title--category"><?php the_archive_title(); ?></h1>
	</div>
<?php endif; ?>


	<div class="content block">
<?php
	if(have_posts()) :

		while (have_posts()) : the_post();

			knife_theme_widget_template([
				'before' => '<div class="widget widget-%s">',
				'after' => '</div>'
			]);

		endwhile;

	else:

		// Include "no posts found" template
		get_template_part('template-parts/content/post', 'none');

	endif;
?>
	</div>


<?php if(have_posts()) : ?>
	<div class="nav block">
		<?php next_posts_link('Больше статей'); ?>
	</div>
<?php endif; ?>

</main>


<?php

get_footer();
