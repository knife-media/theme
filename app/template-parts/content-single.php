<?php
/**
 * Single content template
 *
 * @package knife-theme
 * @since 1.1
 */

?>
<div class="content__inner container">
	<article class="post" id="post-<?php the_ID(); ?>">
		<div class="post__inner entry">
			<?php get_template_part('template-parts/entry', 'header'); ?>

			<h1 class="entry__title">
				<?php the_title(); ?>
			</h1>

			<?php if(!in_category('news')) : ?>
			<div class="entry__excerpt">
			  <?php the_excerpt(); ?>
			</div>
			<?php endif; ?>

			<div class="entry__content custom">
				<?php the_content(); ?>
			</div>

			<?php get_template_part('template-parts/entry', 'tags'); ?>

			<?php get_template_part('template-parts/entry', 'related'); ?>
		</div>
	</article>

	<?php get_sidebar('right'); ?>
</div>
