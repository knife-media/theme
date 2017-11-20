<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">

	<section class="content__archive block">

	<?php
		// Include "no posts found" template
		get_template_part('template-parts/post/content', 'none');
	?>

	</section>

</main>


<?php

get_footer();
