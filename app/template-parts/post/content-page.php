<?php
/**
 * Content template using basically for pages
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="post post--wide" id="post-<?php the_ID(); ?>">
	<div class="post__inner entry">

		<div class="entry__content custom">
			<?php the_content(); ?>
		</div>

	</div>
</article>
