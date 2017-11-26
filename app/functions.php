<?php
/**
 * Important functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * @package knife-theme
 * @since 1.1
 */


// We have to install this value
if(!isset($content_width)) {
	$content_width = 650;
}

// Insert required js files
add_action('wp_enqueue_scripts', function() {
 	$version = wp_get_theme()->get('Version');

	wp_enqueue_script('knife-scripts', get_template_directory_uri() . '/assets/scripts.min.js', [], $version, true);
});


// Insert styles
add_action('wp_print_styles', function() {
	$version = wp_get_theme()->get('Version');

   	wp_enqueue_style('knife-styles', get_template_directory_uri() . '/assets/styles.min.css', [], $version);
});


// Change mail from fields
add_filter('wp_mail_from_name', function($name) {
	return __('knife.media webmaster', 'knife-theme');
});

add_filter('wp_mail_from', function($email) {
	$hostname = parse_url(site_url("/"), PHP_URL_HOST);

	return "no-reply@{$hostname}";
});


// Remove useless widgets from wp-admin
add_action('admin_init', function() {
	remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
	remove_meta_box('dashboard_primary', 'dashboard', 'normal');
});


// Add required theme support tags
add_action('after_setup_theme', function() {
	// Post formats
	add_theme_support('post-formats', ['aside']);

	// Something useful
	add_theme_support('html5', ['caption']);

	// Let wordpress generate page title
	add_theme_support('title-tag');

	// Add links to feeds in header
	add_theme_support('automatic-feed-links');
});


// Custom image sizes
add_action('after_setup_theme', function(){
	add_theme_support('post-thumbnails');

	add_image_size('medium-thumbnail', 480, 99999, false);
	add_image_size('related-thumbnail', 360, 180, true);
	add_image_size('fullscreen-thumbnail', 1920, 1080, true);

	add_image_size( 'cb-100-65', 100, 65, true );
	add_image_size( 'cb-260-170', 260, 170, true );
	add_image_size( 'cb-360-490', 360, 490, true );
	add_image_size( 'cb-360-240', 360, 240, true );
	add_image_size( 'cb-378-300', 378, 300, true );
	add_image_size( 'cb-759-300', 759, 300, true );
	add_image_size( 'cb-759-500', 759, 500, true );
	add_image_size( 'cb-759-600', 759, 600, true );
	add_image_size( 'cb-1400-600', 1400, 600, true );
});


// Add theme menus
add_action('after_setup_theme', function() {
	register_nav_menus([
		'main' => __('Верхнее меню', 'knife-theme'),
		'footer' => __('Нижнее меню', 'knife-theme'),
		'social' => __('Меню социальных ссылок', 'knife-theme')
	]);
});


// Remove fcking emojis and wordpress meta for security reasons
add_action('init', function() {
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link' );
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0 );
	remove_action('wp_head', 'rest_output_link_wp_head', 10 );
	remove_action('wp_head', 'wp_oembed_add_discovery_links', 10 );
});


// Disable embeds
add_action('wp_enqueue_scripts', function() {
	wp_deregister_script('wp-embed');
});


// Disable jquery
add_action('wp_enqueue_scripts', function() {
	if(!is_user_logged_in()) {
		wp_deregister_script('jquery');
	}
});


// Rewrite urls after switch theme just in case
add_action('after_switch_theme', function() {
     flush_rewrite_rules();
});


// We don't want to use default gallery styles anymore
add_filter('use_default_gallery_style', '__return_false');


// For the reason that we don't use comments in this theme we have to remove comments feed link from header
add_filter('feed_links_show_comments_feed', '__return_false');


// Navigation links classes
add_filter('next_posts_link_attributes', function($atts) {
	return 'class="nav__link nav__link--next"';
});

add_filter('previous_posts_link_attributes', function($atts) {
	return 'class="nav__link nav__link--prev"';
});


// Update authors contact info
add_filter('user_contactmethods', function($contact) {
	$contact['vkontakte'] = __('Ссылка на ВКонтакте', 'knife-theme');
	$contact['facebook'] = __('Ссылка на Facebook', 'knife-theme');
 	$contact['telegram'] = __('Профиль Telegram', 'knife-theme');
 	$contact['instagram'] = __('Профиль Instagram', 'knife-theme');
 	$contact['twitter'] = __('Профиль Twitter', 'knife-theme');

	return $contact;
});


// Change default menu items class
add_filter('nav_menu_css_class', function($classes, $item, $args) {
	if($args->theme_location === 'main')
		return ['topline__menu-item'];

	if($args->theme_location === 'footer')
		return ['footer__menu-item'];

	if($args->theme_location === 'social')
		return ['social__item'];

	return $classes;
}, 10, 3);


// Add class to menu item link
add_filter('nav_menu_link_attributes', function($atts, $item, $args) {
	if($args->theme_location === 'main')
	 	$atts['class'] = 'topline__menu-link';

 	if($args->theme_location === 'footer')
	 	$atts['class'] = 'footer__menu-link';

	if($args->theme_location === 'social')
		$atts['class'] = 'social__item-link';

	return $atts;
}, 10, 3);


// We have to change titles to icons in social menu
add_filter('nav_menu_item_title', function($title, $item, $args) {
	if($args->theme_location === 'social')
		return sprintf('<span class="icon icon--%1$s" title="%1$s"></span>', strtolower($title));

	return $title;
}, 10, 3);


// Remove menu ids
add_filter('nav_menu_item_id', '__return_empty_string');


// Rename aside post format
add_filter('gettext_with_context', function($translation, $text, $context, $domain) {
	$names = [
		'Standard' => __('Стандартный', 'knife-theme'),
		'Aside'  => __('Без сайдбара', 'knife-theme'),
		'Video' => __('Видео', 'knife-theme'),
 		'Audio' => __('Аудио', 'knife-theme')
	];

	if($context !== 'Post format')
		return $translation;

	return str_replace(array_keys($names), array_values($names), $text);
}, 10, 4);


// Remove annoying [...] in excerpts
add_filter('excerpt_more', function($more) {
	return '&hellip;';
});


// Archive title fix
add_filter('get_the_archive_title', function($title) {
	if(is_category() || is_tag() || is_tax())
		return single_term_title('', false);

	return $title;
});


// Disable post attachment pages
// Redirect to post parent if exists
add_action('template_redirect', function() {
	global $post;

	if(!is_attachment())
		return false;

	if(isset($post->post_parent) && $post->post_parent > 0)
		$url = get_permalink($post->post_parent);
	else
		$url = home_url('/');

	wp_redirect(esc_url($url), 301);
	exit;
});

add_filter('attachment_link', function() {
	return;
});


// Remove private posts from archives and home page.
// Note: Knife editors use private posts as drafts. So we don't want to see drafts in templates even if we have logged in
add_action('pre_get_posts', function($query) {
	if($query->is_main_query() && ($query->is_archive() || $query->is_home()))
		$query->set('post_status', 'publish');
});


// Remove useless image attributes
add_filter('post_thumbnail_html', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('image_send_to_editor', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);


// Register widget area.
add_action('widgets_init', function(){
	register_sidebar([
		'name'          => __('Главная страница', 'knife-theme'),
		'id'            => 'knife-frontal',
		'description'   => __('Добавленные виджеты появятся на главной странице под телевизором, если он не пуст.', 'knife-theme'),
		'before_widget' => '<div class="widget widget--%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>',
	]);

	register_sidebar([
		'name'          => __('Телевизор на главной', 'knife-theme'),
		'id'            => 'knife-feature',
		'description'   => __('Добавленные виджеты появятся в телевизоре на главной странице.', 'knife-theme'),
		'before_widget' => '<div class="widget widget--%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>',
	]);

	register_sidebar([
		'name'          => __('Подвал сайта', 'knife-theme'),
		'id'            => 'knife-footer',
		'description'   => __('Добавленные виджеты появятся справа в футере.', 'knife-theme'),
		'before_widget' => '<aside class="widget widget--text">',
		'after_widget'  => '</aside>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>',
	]);

 	register_sidebar([
		'name'          => __('Сайдбар на главной', 'knife-theme'),
		'id'            => 'knife-feature-sidebar',
		'description'   => __('Добавленные виджеты появятся справа от телевизона.', 'knife-theme'),
		'before_widget' => '<div class="widget widget--%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>',
	]);

 	register_sidebar([
		'name'          => __('Сайдбар на внутренних', 'knife-theme'),
		'id'            => 'knife-inner-sidebar',
		'description'   => __('Добавленные виджеты появятся в сайдбаре внутри постов.', 'knife-theme'),
		'before_widget' => '<div class="widget widget--%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>',
	]);
});


// Hide default widgets title
add_filter('widget_title', '__return_empty_string');


// Remove default widgets to prevent printing unready styles on production
add_action('widgets_init', function() {
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Links');
	unregister_widget('WP_Widget_Meta');
	unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Categories');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Tag_Cloud');
	unregister_widget('WP_Nav_Menu_Widget');
}, 11);


// Custom template tags for this theme.
require get_template_directory() . '/inc/helpers/template-tags.php';

// Add post settings rules and admin metaboxes
require get_template_directory() . '/inc/helpers/post-settings.php';

// Add custom widgets defenitions
require get_template_directory() . '/inc/widgets/recent.php';
require get_template_directory() . '/inc/widgets/related.php';
require get_template_directory() . '/inc/widgets/stripe.php';
require get_template_directory() . '/inc/widgets/mindmap.php';
require get_template_directory() . '/inc/widgets/transparent.php';
