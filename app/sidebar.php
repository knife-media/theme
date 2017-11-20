<?php
/**
 * Single template sidebar
 *
 * Show last news and related posts
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<aside class="sidebar">
	<?php get_template_part('template-parts/sidebar/sidebar', 'news'); ?>

 	<?php get_template_part('template-parts/sidebar/sidebar', 'related'); ?>
</aside>
