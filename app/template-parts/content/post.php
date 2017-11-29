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
				'after' => '</div>',
			]);

			the_title(
				'<h1 class="post__header-title">',
				'</h1>'
			);

			knife_theme_excerpt([
				'before' => '<div class="post__header-excerpt">',
				'after' => '</div>'
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
				'next_or_number' => 'next'
			]);

			knife_theme_tags([
				'before' => '<div class="post__footer-tags refers">',
				'after' => '</div>',
				'item' => '<a class="refers__link" href="%2$s">%1$s</a>',
				'between' => ''
			]);

 			knife_theme_related([
				'before' => '<div class="post__footer-related refers">',
				'after' => '</div>',
				'title' =>  '<div class="refers__title">%s</div>',
				'item' => '<div class="refers__item"><a class="refers__item-link" href="%1$s">%2$s</a></div>'
			]);
		?>
	</footer>
</article>

<?php get_sidebar(); ?>
