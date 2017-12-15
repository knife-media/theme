<?php
/**
 * Recent news widget template
 *
 * Typically used in sidebar to print last news
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="widget__item">
	<?php
		knife_theme_meta([
			'items' => ['date', 'tag'],
			'before' => '<div class="widget__meta meta">',
			'after' => '</div>'
		]);

		printf(
			'<a class="widget__link" href="%1$s">%2$s</a>',
			get_permalink(),
			get_the_title()
		)
	?>
</article>
