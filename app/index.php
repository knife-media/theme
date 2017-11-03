<?php
/**
 * The main template file
 *
 * @package knife-theme
 * @since 1.1
 */
get_header(); ?>

<div class="container px-0">
	<div id="arch_cont" class="grid grid--small-gaps grid--lg-modules">
		<?php
			if ( have_posts() ) :

				while (have_posts()) : the_post();

					// Include specific content template
					get_template_part('templates-part/content', 'news-norm');

				endwhile;

			else:

				// Include "no posts fount" template
				get_template_part('templates-part/content', 'none');

			endif;
		?>
	</div>
</div>

<?php

get_footer();
