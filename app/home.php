<?php
/**
 * The template for displaying main archive
 *
 * List of articles ordered by asc on replacement front page to show all posts.
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="content">

	<?php if(have_posts()) : ?>
		<header class="content__header block">
			<h1 class="content__header-title"><?php _e('Последние статьи', 'knife-theme'); ?></h1>
		</header>

	<?php endif; ?>

	<section class="content__archive block">
<?php
	if(have_posts()) :

		while (have_posts()) : the_post();

			// Include triple unit template as best suitable
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
