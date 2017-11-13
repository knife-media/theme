<?php
/**
 * Mindmap stripe contains only post title
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="stripe__item">
	<a class="stripe__item-link" href="<?php the_permalink(); ?>">
		<p class="stripe__item-title"><?php the_title(); ?></p>
	</a>
</article>
