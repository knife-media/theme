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
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="header">
	<div class="header__topline topline container">
 		<button class="topline__button topline__button--menu toggle" role="button">
			<span class="toggle__line"></span>
			<span class="toggle__line"></span>
			<span class="toggle__line"></span>
		</button>

		<a class="topline__logo" href="<?php echo esc_url(home_url('/')); ?>">
			<img class="topline__logo-image" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.svg" alt="<?php bloginfo('name'); ?>">
		</a>

		<nav class="topline__menu">
			<?php
				wp_nav_menu([
					'theme_location' => 'main_menu',
					'depth' => 1,
					'echo' => true,
					'items_wrap' => '<ul class="topline__menu-list">%3$s</ul>',
					'container' => false
				]);
			?>

			<div class="topline__menu-social">
				<?php get_template_part('template-parts/meta', 'social'); ?>
			</div>
		</nav>

    	<button class="topline__button topline__button--search" role="button">
			<span class="icon icon--search-fat"></span>
		</button>
	</div>
</header>

<?php /*
	<header class="header" id="header">
		<div class="container header__inner navbar navbar-expand-lg">
			<div class="header__mobile-group">
				<div id="menuBurger" class="burger header__burger">
					<div class="burger__stick"></div>
					<div class="burger__stick"></div>
					<div class="burger__stick"></div>
				</div>



				<a href="#" class="header__mobile-search-btn search-btn">
					<span class="icon icon--search"></span>
				</a>
			</div>

			<nav class="header__nav">
				<?php
					wp_nav_menu([
						'theme_location' => 'main_menu',
						'depth' => 1,
						'echo' => true,
						'items_wrap' => '<ul class="header__menu menu">%3$s</ul>',
						'container' => false
					]);
				?>

				<div class="socials header__socials">
					<ul class="socials__list">
						<li class="socials__item">
							<a href="https://www.facebook.com/theknifemedia" class="socials__link">
								<span class="icon icon--fb"></span>
							</a>
						</li>
						<li class="socials__item">
							<a href="https://vk.com/knife.media" class="socials__link">
 								<span class="icon icon--vk"></span>
							</a>
						</li>
						<li class="socials__item">
							<a href="http://telegram.me/knifemedia" class="socials__link">
 								<span class="icon icon--telegram"></span>
							</a>
						</li>
						<li class="socials__item">
							<a href="https://twitter.com/knife_media" class="socials__link">
  								<span class="icon icon--twitter"></span>
							</a>
						</li>
						<li class="socials__item">
							<a id="searchBtn" href="#" class="socials__link brand-link header__socials-link header__search-btn search-btn">
								<i class="icon icon--search-fat" aria-hidden="true"></i>
								<i class="fa fa-times search-btn__close-icon" aria-hidden="true"></i>
							</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</header>
 */?>
