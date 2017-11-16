<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">
	<header class="content__header block">
		<?php
			the_archive_title('<h1 class="content__header-title">', '</h1>');
		?>
	</header>

	<section class="content__archive block">
<?php
	if ( have_posts() ) :

		while (have_posts()) : the_post();

			if($wp_query->current_post % 5 === 3 || $wp_query->current_post % 5 === 4)
				knife_theme_unit('double');
			else
 				knife_theme_unit('triple');

		endwhile;

	endif;
?>
	</section>
</main>


<?php

get_footer();
