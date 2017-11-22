<?php
/**
 * Content template using basically for pages
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<div class="entry">
	<article class="post" id="post-<?php the_ID(); ?>">

		<div class="entry__content custom">
			<?php the_content(); ?>
		</div>

	</article>
</div>
