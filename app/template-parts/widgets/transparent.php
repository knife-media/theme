<?php
/**
 * Transparent widget template
 *
 * Transparent is a transparent stripe with optional sticker
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article class="widget__item">
	<?php
		knife_theme_meta([
			'opts' => ['head'],
			'before' => '<div class="widget__head">',
			'after' => '</div>',
			'item' => '<span class="widget__head-item">%s</span>',
			'link' => '<a class="widget__head-link" href="%2$s">%1$s</a>'
		]);
	?>

	<div class="widget__parent">
		<?php
			knife_theme_post_meta([
				'item' => '<img class="widget__sticker" src="%s">',
				'meta' => 'post-sticker'
			]);
		?>

		<footer class="widget__footer">
			<?php
				knife_theme_meta([
					'opts' => ['author', 'date'],
					'before' => '<div class="widget__meta meta">',
					'after' => '</div>'
				]);

				printf(
					'<a class="widget__link" href="%2$s">%1$s</a>',
					the_title('<p class="widget__title">', '</p>', false),
					get_permalink()
				);
			?>
		</footer>
	</div>
</article>
