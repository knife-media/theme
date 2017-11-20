<?php
/**
 * Page template
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">

	<section class="content__single block">
<?php
	if (have_posts()) :

		while (have_posts()) : the_post();

			// Include specific content template
			get_template_part('template-parts/post/content', 'page');

		endwhile;

	else:

		// Include "no posts found" template
		get_template_part('template-parts/post/content', 'none');

	endif;
?>
	</section>

</main>


<?php

get_footer();
