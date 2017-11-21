<?php
/**
 * The main template file
 *
 * Most likely this template will never be shown.
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package knife-theme
 * @since 1.1
 */
get_header(); ?>

<main class="wrap">

	<div class="content block">

	<?php
		// Include "no posts found" template
		get_template_part('template-parts/content/post', 'none');
	?>

	</div>

</main>


<?php

get_footer();
