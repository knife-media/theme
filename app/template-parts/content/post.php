<?php
/**
 * Standart post format content template width sidebar
 *
 * @package knife-theme
 * @since 1.1
 */
?>


<article <?php post_class('entry'); ?> id="post-<?php the_ID(); ?>">
	<header class="entry__header entry__block">
		<div class="entry__header-meta meta">
			<?php
				if(function_exists('coauthors_posts_links')) :
					coauthors_posts_links();
				else :
					the_author_posts_link();
				endif;
			?>

			<time class="meta__item" datetime="<?php the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>
		</div>

		<?php the_title('<h1 class="entry__header-title">', '</h1>'); ?>

		<?php if(has_excerpt() && !in_category('news')) : ?>
			<div class="entry__header-excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="entry__content custom">
		<?php the_content(); ?>
	</div>

	<footer class="entry__footer">
		<?php
			wp_link_pages(['before' => '<div class="entry__navigation">', 'after' => '</div>', 'next_or_number' => 'next']);

			/**
			 * Print share buttons, terms and related posts
			 */
//			knife_theme_entry_share();

			// TODO: rework with knife_theme_tags
//			knife_theme_entry_tags();

//			knife_theme_entry_related();
		?>
	</footer>
</article>

<?php get_sidebar(); ?>
