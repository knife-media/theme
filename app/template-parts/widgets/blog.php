<?php
/**
 * Story widget template
 *
 * @package knife-theme
 * @since 1.3
 */


$ids = get_query_var('widget_posts', false);
print_r($ids);


	$q = new WP_Query([
		'post_type' => 'blog',
    	'post__in' => get_query_var('widget_posts', [])
	]);

	while($q->have_posts()) : $q->the_post();
echo '123';
?>
<article>
	<footer class="widget__footer">
		<?php
			printf(
				'<a class="widget__link" href="%2$s">%1$s</a>',
				the_title('<p class="widget__title">', '</p>', false),
				get_permalink()
			);
		?>
	</footer>
</article>
<?php
	endwhile;

?>
