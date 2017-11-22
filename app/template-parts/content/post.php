<?php
/**
 * Standart post format content template width sidebar
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<div class="entry">
	<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

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
			wp_link_pages(['before' => '<div class="entry__navigation">', 'after' => '</div>', 'next_or_number' => 'next']);

			/**
			 * Print share buttons, terms and related posts
			 */
			knife_theme_entry_share();

			// TODO: rework with knife_theme_tags
			knife_theme_entry_tags();

			knife_theme_entry_related();
		?>
	</article>
</div>

<?php get_sidebar(); ?>
