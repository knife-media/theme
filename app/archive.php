<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">

	<?php if(have_posts()) : ?>
		<header class="content__header block">
			<?php the_archive_title('<h1 class="content__header-title">', '</h1>'); ?>
		</header>

	<?php endif; ?>

	<section class="content__archive block">

	<?php
		if(have_posts()) :

			while (have_posts()) : the_post();

				if($wp_query->current_post % 5 === 3 || $wp_query->current_post % 5 === 4)
					get_template_part('template-parts/loop/unit', 'double');
				else
					get_template_part('template-parts/loop/unit', 'triple');

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
