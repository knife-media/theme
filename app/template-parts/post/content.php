<?php
/**
 * Standart post format content template width sidebar
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="post" id="post-<?php the_ID(); ?>">
	<div class="post__inner entry">

		<header class="entry__header">
			<?php
				/**
				* Prints entry author, category and date
				*/
				knife_theme_entry_header();
			?>
		</header>

		<?php the_title('<h1 class="entry__title">', '</h1>'); ?>

		<?php if(has_excerpt() && !in_category('news')) : ?>
		<div class="entry__excerpt">
		  <?php the_excerpt(); ?>
		</div>
		<?php endif; ?>

		<?php
			/**
			 * Print share buttons
			 */
			knife_theme_entry_share();
		?>

		<div class="entry__content custom">
			<?php the_content(); ?>
		</div>

		<?php
			/**
			 * Print share buttons, terms and related posts
			 */
			knife_theme_entry_share();

			// TODO: rework with knife_theme_tags
			knife_theme_entry_tags();

			knife_theme_entry_related();
		?>
	</div>
</article>

<?php get_sidebar(); ?>
