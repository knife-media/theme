<?php
/**
 * Template for showing site front-page
 *
 * Difference between front-page and home.php in a link below
 *
 * @link https://wordpress.stackexchange.com/a/110987
 *
 * @package knife-theme
 * @since 1.1
 */

get_header();

?>

<main <?php knife_theme_cover("content", $post->ID); ?>>
	<div class="content__archive block">

<?php if(is_active_sidebar('knife-front')) : ?>
		<?php dynamic_sidebar( 'knife-front' ); ?>
<?php endif; ?>

	</div>
</main>

<?php

get_footer();
