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
	$content_width = 1024;
}

// Insert required js files
add_action('wp_enqueue_scripts', function() {
 	$version = wp_get_theme()->get('Version');
 	$version = time();

	wp_enqueue_script('knife-theme', get_template_directory_uri() . '/assets/scripts.min.js', [], $version, true);
});


// Insert styles
add_action('wp_print_styles', function() {
	$version = wp_get_theme()->get('Version');
	$version = time();

	wp_enqueue_style('knife-theme', get_template_directory_uri() . '/assets/styles.min.css', [], $version);
});


// Rewrite urls after switch theme just in case
add_action('after_switch_theme', function() {
     flush_rewrite_rules();
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

	// Let wordpress generate page title
	add_theme_support('title-tag');

	// Add links to feeds in header
	add_theme_support('automatic-feed-links');

	// Let wordpress manage cutsom background
	add_theme_support('custom-background', ['wp-head-callback' => 'knife_custom_background']);
});


// Custom image sizes
add_action('after_setup_theme', function(){
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size(300, 300, true);

	add_image_size('outer', 1024, 9999, false);
 	add_image_size('inner', 640, 9999, false);

	add_image_size('triple', 480, 360, true);
	add_image_size('double', 640, 480, true);
	add_image_size('single', 1280, 360, true);
});


// We want to use own image sizes in post
add_filter('image_size_names_choose', function($size_names) {
	global $_wp_additional_image_sizes;

	$size_names = array(
		'outer' => __('На всю ширину', 'knife-theme'),
 		'inner' => __('По ширине текста', 'knife-theme')
	);

	return $size_names;
});


// Remove default useless large and medium sizes
add_filter('intermediate_image_sizes', function($def_sizes) {
	unset($def_sizes['medium']);
	unset($def_sizes['large']);

	return $def_sizes;
});


// Disable wordpress captions to replace them by own
add_filter('disable_captions', '__return_true');


// Remove useless image attributes
add_filter('post_thumbnail_html', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('get_image_tag', function($html) {
	return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('get_image_tag_class', function($class, $id, $align, $size) {
	return 'figure__image';
}, 0, 4);


// Wrap all images in editor with figure
add_filter('image_send_to_editor', function($html, $id, $caption, $title, $align, $url, $size, $alt) {
	$html = get_image_tag($id, $alt, '', $align, $size);

	if($url)
		$html = '<a href="' . esc_attr($url) . '">' . $html . '</a>';

	if($caption)
		$html = $html . '<figcaption class="figure__caption">' . $caption . '</figcaption>';

	$html = '<figure class="figure figure--' . $size . ' figure--' . $align . '">' . $html . '</figure>';

	return $html;
}, 10, 9);


// Default embed width
add_filter('embed_defaults', function() {
	return ['width' => 640, 'height' => 525];
});


add_filter('embed_oembed_html', function($html, $url, $attr) {
	$html = '<figure class="figure figure--embed">' . $html . '</figure>';

	return $html;
}, 10, 3);


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
	if(!is_user_logged_in())
		wp_deregister_script('jquery');
}, 11);


// Remove background controls from admin customizer
add_action("customize_register", function($wp_customize) {
	$wp_customize->remove_control('background_preset');
 	$wp_customize->remove_control('background_position');
 	$wp_customize->remove_control('background_size');
 	$wp_customize->remove_control('background_repeat');
	$wp_customize->remove_control('background_attachment');
});

// Print fixed element with custom background
add_action('wp_footer', function() {
	knife_custom_background(true);
});


// Add search popover to footer
add_action('wp_footer', function() {
	get_search_form();
});


// Remove annoying body classes
// It will be better to use body-- class prefix if we need it later
add_filter('body_class', function($wp_classes, $extra_classes) {
	return [];
}, 10, 2);


// Remove annoying post classes
// We can use entry-- clas prefix better
add_filter('post_class', function($classes, $class) {
	return $class;
}, 10, 2);


// Change mail from fields
add_filter('wp_mail_from_name', function($name) {
	return __('knife.media webmaster', 'knife-theme');
});

add_filter('wp_mail_from', function($email) {
	$hostname = parse_url(site_url("/"), PHP_URL_HOST);

	return "no-reply@{$hostname}";
});


// It is good to remove auto suggestings for SEO
// https://core.trac.wordpress.org/ticket/16557
add_filter('redirect_canonical', function($url) {
	if(is_404() && !isset($_GET['p']))
		return false;

	return $url;
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


// Post author link class
add_filter('the_author_posts_link', function($link) {
	return str_replace('rel="author"', 'class="meta__link" rel="author"', $link);
});


// Single post nav links
add_filter('wp_link_pages_link', function($link) {
	return str_replace('href="', 'class="refers__link" href="', $link);
}, 10, 2);


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
 		'Audio' => __('Аудио', 'knife-theme'),
		'Gallery' => __('Галерея', 'knife-theme'),
		'Chat' => __('Карточки', 'knife-theme')
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


// Disable wordpress based search to reduce CPU load and prevent DDOS attacks
add_action('parse_query', function($query) {
	if(!$query->is_search || is_admin())
		return false;

	$query->set('s', '');
	$query->is_search = false;
	$query->is_404 = true;
}, 9);


// Remove private posts from archives and home page.
// Note: Knife editors use private posts as drafts. So we don't want to see drafts in templates even if we have logged in
add_action('pre_get_posts', function($query) {
	if($query->is_main_query() && ($query->is_archive() || $query->is_home()))
		$query->set('post_status', 'publish');
});


// Register widget area.
add_action('widgets_init', function(){
	register_sidebar([
		'name'          => __('Главная страница', 'knife-theme'),
		'id'            => 'knife-frontal',
		'description'   => __('Добавленные виджеты появятся на главной странице под телевизором, если он не пуст.', 'knife-theme'),
		'before_widget' => '<div class="widget widget-%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>'
	]);

	register_sidebar([
		'name'          => __('Телевизор на главной', 'knife-theme'),
		'id'            => 'knife-feature-stripe',
		'description'   => __('Добавленные виджеты появятся в телевизоре на главной странице.', 'knife-theme'),
		'before_widget' => '<div class="widget widget-%2$s widget--split">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'	=> '</p>'
	]);

	register_sidebar([
		'name'          => __('Сквозной под шапкой', 'knife-theme'),
		'id'            => 'knife-header',
		'description'   => __('Добавленные виджеты появятся под шапкой на главной и внутренних страницах.', 'knife-theme'),
		'before_widget' => '<div class="widget widget-%2$s widget--header">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'	=> '</p>'
	]);

	register_sidebar([
		'name'          => __('Подвал сайта', 'knife-theme'),
		'id'            => 'knife-footer',
		'description'   => __('Добавленные виджеты появятся справа в футере.', 'knife-theme'),
		'before_widget' => '<aside class="widget widget-text">',
		'after_widget'  => '</aside>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>'
	]);

 	register_sidebar([
		'name'          => __('Сайдбар на главной', 'knife-theme'),
		'id'            => 'knife-feature-sidebar',
		'description'   => __('Добавленные виджеты появятся справа от телевизора.', 'knife-theme'),
		'before_widget' => '<div class="widget widget-%2$s widget--split">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>'
	]);

 	register_sidebar([
		'name'          => __('Сайдбар на внутренних', 'knife-theme'),
		'id'            => 'knife-inner-sidebar',
		'description'   => __('Добавленные виджеты появятся в сайдбаре внутри постов.', 'knife-theme'),
		'before_widget' => '<div class="widget widget-%2$s widget--sidebar">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="widget__title">',
		'after_title'   => '</p>'
	]);
});


// Hide default widgets title
add_filter('widget_title', '__return_empty_string');


// Register special post taxonomy
add_action('init', function() {
	register_taxonomy('special', 'post', [
		'labels' => [
				'name'                       => __('Спецпроекты', 'knife-theme'),
				'singular_name'              => __('Спецпроект', 'knife-theme'),
				'search_items'               => __('Поиск', 'knife-theme'),
				'popular_items'              => __('Популярные спецпроекты', 'knife-theme'),
				'all_items'                  => __('Все', 'knife-theme'),
				'edit_item'                  => __('Редактировать', 'knife-theme'),
				'update_item'                => __('Обновить', 'knife-theme'),
				'add_new_item'               => __('Добавить новый', 'knife-theme'),
				'new_item_name'              => __('Новый спецпроект', 'knife-theme'),
				'separate_items_with_commas' => __('Разделить записи запятыми', 'knife-theme'),
				'add_or_remove_items'        => __('Добавить или удалить тип', 'knife-theme'),
				'choose_from_most_used'      => __('Наиболее используемые', 'knife-theme'),
				'not_found'                  => __('Не найдено', 'knife-theme'),
				'menu_name'                  => __('Спецпроекты', 'knife-theme'),
		],
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array('slug' => 'special'),
	]);
});

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

// Custom theme shortcodes
require get_template_directory() . '/inc/helpers/theme-shortcodes.php';

// Add plugins snippets
require get_template_directory() . '/inc/helpers/plugin-snippets.php';

// Login screen custom styles
require get_template_directory() . '/inc/helpers/login-screen.php';

// Add custom widgets defenitions
require get_template_directory() . '/inc/widgets/recent.php';
require get_template_directory() . '/inc/widgets/stripe.php';
require get_template_directory() . '/inc/widgets/feature.php';
require get_template_directory() . '/inc/widgets/details.php';
require get_template_directory() . '/inc/widgets/transparent.php';
