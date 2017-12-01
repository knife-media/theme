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

get_header(); ?>

<main class="wrap">

	<?php if(is_active_sidebar('knife-feature')) : ?>

		<div class="content block">
			<div class="feature">
				<?php dynamic_sidebar('knife-feature'); ?>
			</div>

			<?php if(is_active_sidebar('knife-feature-sidebar')) : ?>
			<aside class="sidebar sidebar--feature">
				<?php dynamic_sidebar('knife-feature-sidebar'); ?>
			</aside>
			<?php endif; ?>
		</div>

	<?php endif; ?>

	<?php if(is_active_sidebar('knife-frontal')) : ?>

		<div class="content block">
			<?php dynamic_sidebar('knife-frontal'); ?>
		</div>

	<?php endif; ?>

</main>

<?php

get_footer();
