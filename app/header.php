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

	<header class="header" id="header">
		<div class="container header__inner navbar navbar-expand-lg">
			<div class="header__mobile-group">
				<div id="menuBurger" class="burger header__burger" data-toggle="collapse" data-target="#main-nav">
					<div class="burger__stick"></div>
					<div class="burger__stick"></div>
					<div class="burger__stick"></div>
				</div>

				<a href="<?php echo esc_url(home_url('/')); ?>" class="logo mx-auto header__logo">
					<img class="logo__img" src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.svg" alt="<?php bloginfo( 'name' ); ?>">
				</a>

				<a id="mobileSearchBtn" href="#" class="header__mobile-search-btn search-btn">
					<img class="search-btn__search-icon" src="<?php echo $themes_url;?>/src/assets/imgs/icons/search.svg" />
					<svg width="18px" class="search-btn__close-icon" x="0px" y="0px" viewBox="0 0 20 20">
					<path d="M11.4,10l8.2-8.2c0.4-0.4,0.4-1,0-1.4s-1-0.4-1.4,0L10,8.6L1.7,0.3c-0.4-0.4-1-0.4-1.4,0
						 s-0.4,1,0,1.4L8.6,10l-8.3,8.3c-0.4,0.4-0.4,1,0,1.4s1,0.4,1.4,0l8.3-8.3l8.3,8.3c0.4,0.4,1,0.4,1.4,0s0.4-1,0-1.4L11.4,10z"/>
					</svg>
				</a>
			</div>

			<nav class="header__nav collapse navbar-collapse" id="main-nav">
			<?php
				wp_nav_menu([
					'theme_location' => 'main_menu',
					'depth' => 2,
					'echo' => true,
					'items_wrap' => '<ul class="menu sm header__menu">%3$s</ul>',
					'container' => false
				]);
			?>

				<div class="socials header__socials">
					<ul class="socials__list">
						<li class="socials__item">
							<a href="https://www.facebook.com/theknifemedia" class="socials__link brand-link"><i class="fa fa-facebook socials__fb" aria-hidden="true"></i></a>
						</li>
						<li class="socials__item">
							<a href="https://vk.com/knife.media" class="socials__link brand-link"><i class="fa fa-vk socials__vk" aria-hidden="true"></i></a>
						</li>
						<li class="socials__item">
							<a href="http://telegram.me/knifemedia" class="socials__link brand-link"><i class="fa fa-telegram socials__tg" aria-hidden="true"></i></a>
						</li>
						<li class="socials__item">
							<a href="https://twitter.com/knife_media" class="socials__link brand-link"><i class="fa fa-twitter socials__tw" aria-hidden="true"></i></a>
						</li>
						<li class="socials__item">
							<a id="searchBtn" href="#" class="socials__link brand-link header__socials-link header__search-btn search-btn">
								<i class="fa fa-search search-btn__search-icon" aria-hidden="true"></i>
								<i class="fa fa-times search-btn__close-icon" aria-hidden="true"></i>
							</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>
	</header>

	<main>
