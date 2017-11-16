<?php
/**
 * Template for display single post
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">
<?php
	if ( have_posts() ) :

		while (have_posts()) : the_post();

			// Include specific content template
			get_template_part('template-parts/content', get_post_format());

		endwhile;

	else:

		// Include "no posts found" template
		get_template_part('template-parts/content', 'none');

	endif;
?>
</main>


<?php

get_footer();
