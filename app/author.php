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

				if($wp_query->current_post % 5 === 3 || $wp_query->current_post % 5 === 4)
					get_template_part('template-parts/units/double');
				else
					get_template_part('template-parts/units/triple');

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
