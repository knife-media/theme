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

<main class="content">
	<section class="content__archive block">
<?php
	if(have_posts()) :

		while (have_posts()) : the_post();
			knife_theme_unit('row');
		endwhile;

	endif;
?>
		</div>
	</section>
</main>


<?php

get_footer();
