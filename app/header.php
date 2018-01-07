<?php
/**
 * The template for displaying the header
 *
 * @package knife-theme
 * @since 1.1
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#111111">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="header">
	<div class="header__topline topline block">
 		<button class="topline__button topline__button--menu toggle toggle--menu" id="toggle-menu" role="button">
			<span class="toggle__line"></span>
 			<span class="toggle__line"></span>
 			<span class="toggle__line"></span>
		</button>

		<div class="topline__logo">
			<a class="topline__logo-link" href="<?php echo esc_url(home_url('/')); ?>">
				<svg class="topline__logo-image" fill="#000000" x="0" y="0" viewBox="0 0 111 31.8" xml:space="preserve">
					<g>
						<path d="M27.4,0.6v30.7h-8V19.1H8v12.2H0V0.6h8v11.4h11.4V0.6H27.4z"/>
						<path d="M63.4,15.9C63.4,25,58,31.8,48,31.8c-9.9,0-15.4-6.8-15.4-15.9C32.7,6.8,38.1,0,48,0
							C58,0,63.4,6.8,63.4,15.9z M55.2,15.9c0-5.2-2.4-8.9-7.2-8.9s-7.2,3.7-7.2,8.9c0,5.2,2.4,8.9,7.2,8.9S55.2,21.1,55.2,15.9z"/>
						<path d="M84.9,0.6h7.7v11.5H98l4.6-11.5h8l-6.1,15.1l6.5,15.6h-8l-4.9-12h-5.4v12h-7.7v-12h-5.4l-4.9,12h-8
							l6.5-15.6L67,0.6h8l4.6,11.5h5.3V0.6z"/>
					</g>
				</svg>
			</a>
		</div>

		<nav class="topline__menu">
			<?php
				if(has_nav_menu('main')) :
					wp_nav_menu([
						'theme_location' => 'main',
						'depth' => 1,
						'echo' => true,
						'items_wrap' => '<ul class="topline__menu-list">%3$s</ul>',
						'container' => false
					]);
				endif;

				if(has_nav_menu('social')) :
					wp_nav_menu([
						'theme_location' => 'social',
						'depth' => 1,
						'echo' => true,
						'items_wrap' => '<ul class="social">%3$s</ul>',
						'container_class' => 'topline__menu-social'
					]);
				endif;
			?>
		</nav>

		<button class="topline__button topline__button--search toggle toggle--search" id="toggle-search" role="button">
 			<span class="toggle__line"></span>
 			<span class="toggle__line"></span>
			<span class="toggle__icon icon icon--search"></span>
		</button>
	</div>
</header>
