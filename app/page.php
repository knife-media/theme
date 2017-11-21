<?php
/**
 * Page template
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

	<div class="content block">
<?php
	if (have_posts()) :

		while (have_posts()) : the_post();

			// Include specific content template
			get_template_part('template-parts/content/post', 'page');

		endwhile;

	else:

		// Include "no posts found" template
		get_template_part('template-parts/content/post', 'none');

	endif;
?>
	</div>

</main>


<?php

get_footer();
