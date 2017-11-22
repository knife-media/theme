<?php
/**
 * News template
 *
 * Shows only posts from news category
 * Looks different than archive template
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

				// Include unit template for news
				get_template_part('template-parts/units/row');

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
