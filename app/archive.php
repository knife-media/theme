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

		<div class="stripe stripe--triple">
<?php
	if ( have_posts() ) :

		while (have_posts()) : the_post();

			get_template_part('template-parts/stripe', '');

		endwhile;

	endif;
?>
		</div>
	</section>
</main>


<?php

get_footer();
