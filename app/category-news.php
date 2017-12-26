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

			knife_theme_widget_template([
				'template' => 'template-parts/widgets/logbook',
				'before' => '<div class="widget widget-logbook">',
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
	<div class="nav block block--column">
		<?php posts_nav_link('&bull;', '<span class="icon icon--left"></span>', '<span class="icon icon--right"></span>'); ?>
	</div>
<?php endif; ?>

</main>


<?php

get_footer();
