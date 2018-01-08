<?php
/**
 * Author posts template
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

<?php if(have_posts()) : ?>
	<div class="caption block">
		<h1 class="caption__title caption__title--author"><?php the_author(); ?></h1>

		<div class="caption__text">
			<?php the_author_meta('description'); ?>
		</div>
	</div>
<?php endif; ?>


	<div class="content block">
<?php
	if(have_posts()) :

		while (have_posts()) : the_post();

			knife_theme_widget_template([
				'before' => '<div class="widget widget-%s">',
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
	<div class="nav block">
		<?php next_posts_link('Больше статей'); ?>
	</div>
<?php endif; ?>

</main>


<?php

get_footer();
