<?php
/**
 * Entry header template
 *
 * @package knife-theme
 * @since 1.1
 */

$category = get_the_category(); ?>

<header class="entry__header">
	<?php
		if(function_exists('coauthors_posts_links'))
			coauthors_posts_links();
		else
			the_author_posts_link();
	?>

	<time class="entry__header-meta" datetime="<?php the_time('c'); ?>"><?php the_time('d F Y');?></time>

	<?php if(isset($category[0])) : ?>
	<a class="entry__header-meta" href="<?php echo get_category_link($category[0]->term_id); ?>"><?php echo $category[0]->cat_name; ?></a>
	<?php endif; ?>
</header>
