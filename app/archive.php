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
			<?php the_archive_title('<h1 class="caption__title">', '</h1>'); ?>
		</div>

	<?php endif; ?>

	<div class="content block">

	<?php
		if(have_posts()) :

			while (have_posts()) : the_post();

				if($wp_query->current_post % 5 === 3 || $wp_query->current_post % 5 === 4)
					set_query_var('widget_settings', ['widget--double']);
				else
 					set_query_var('widget_settings', ['widget--triple']);

				get_template_part('template-parts/widgets/stripe');

			endwhile;


		else:

			// Include "no posts found" template
			get_template_part('template-parts/content/post', 'none');

		endif;
	?>

	</div>

	<div class="nav block">
		<?php posts_nav_link('&bull;', '<span class="icon icon--left"></span>', '<span class="icon icon--right"></span>'); ?>
	</div>

</main>


<?php

get_footer();
