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

<main class="wrap">

	<?php if(have_posts()) : ?>

		<div class="caption block">
			<h1 class="caption__title"><?php _e('Последние статьи', 'knife-theme'); ?></h1>
		</div>

	<?php endif; ?>

	<div class="content block">

<?php
	if(have_posts()) :

		while (have_posts()) : the_post();

			// Include triple unit template as best suitable
			get_template_part('template-parts/loop/unit', 'triple');

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
