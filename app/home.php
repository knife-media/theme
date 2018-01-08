<?php
/**
 * The template for displaying main archive
 *
 * List of articles ordered by asc on replacement front page to show all posts.
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

	<div class="content block">
<?php
	if(have_posts()) :

		while (have_posts()) : the_post();

			knife_theme_widget_template([
				'before' => '<div class="widget widget-%1$s">',
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
