<?php
/**
 * Standart post format content template width sidebar
 *
 * @package knife-theme
 * @since 1.1
 */
?>


<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">

	<header class="post__header">
		<?php
			knife_theme_meta([
				'before' => '<div class="post__header-meta meta">',
				'after' => '</div>'
			]);

			the_title(
				'<h1 class="post__header-title">',
				'</h1>'
			);

			knife_theme_post_meta([
				'before' => '<div class="post__header-excerpt">',
				'after' => '</div>',
				'meta' => 'lead-text'
			]);

			knife_theme_share([
				'before' => '<div class="post__header-share share">',
				'after' => '</div>',
				'title' => ''
			]);
		?>
	</header>

	<div class="post__content custom">
		<?php the_content(); ?>
	</div>

	<footer class="post__footer">
		<?php
			wp_link_pages([
				'before' => '<div class="post__footer-nav refers">',
				'after' => '</div>',
				'next_or_number' => 'next',
				'nextpagelink' => __('Следующая страница', 'knife-theme'),
				'previouspagelink' => __('Назад', 'knife-theme')
			]);

			knife_theme_share([
				'before' => '<div class="post__footer-share share">',
				'after' => '</div>'
			]);

			knife_theme_tags([
				'before' => '<div class="post__footer-tags refers">',
				'after' => '</div>',
				'item' => '<a class="refers__link" href="%2$s">%1$s</a>'
			]);

 			knife_theme_related([
				'before' => '<div class="post__footer-related refers">',
				'after' => '</div>'
			]);
		?>
	</footer>
</article>

<?php get_sidebar(); ?>
