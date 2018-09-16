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

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    wp_enqueue_script('knife-theme', get_template_directory_uri() . '/assets/scripts.min.js', [], $version, true);
});


// Insert styles
add_action('wp_print_styles', function() {
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

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
    add_theme_support('post-formats', ['aside', 'chat']);

    // Let wordpress generate page title
    add_theme_support('title-tag');

    // Add links to feeds in header
    add_theme_support('automatic-feed-links');

    // Let wordpress manage cutsom background
    add_theme_support('custom-background', ['wp-head-callback' => '__return_false']);
});


// Custom image sizes
add_action('after_setup_theme', function(){
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(300, 300, true);

    add_image_size('outer', 1024, 9999, false);
    add_image_size('inner', 640, 9999, false);

    add_image_size('ground', 1600, 900, true);
    add_image_size('triple', 480, 360, true);
    add_image_size('double', 640, 480, true);
    add_image_size('single', 1280, 360, true);
});


add_filter('max_srcset_image_width', function($width) {
    return 640;
});


// We want to use own image sizes in post
add_filter('image_size_names_choose', function($size_names) {
    global $_wp_additional_image_sizes;

    $size_names = [
        'outer' => __('На всю ширину', 'knife-theme'),
        'inner' => __('По ширине текста', 'knife-theme'),
        'full'  => __('Исходный размер', 'knife-theme'),
        'short' => __('Обрезанный по высоте', 'knife-theme'),
        'thumbnail' => __('Миниатюра', 'knife-theme')
    ];

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


// Wrap all images in editor with figure
add_filter('image_send_to_editor', function($html, $id, $caption, $title, $align, $url, $size, $alt) {
    $html = get_image_tag($id, $alt, '', $align, $size);

    if($url) {
        $html = '<a href="' . esc_attr($url) . '">' . $html . '</a>';
    }

    if($caption) {
        $html = $html . '<figcaption class="figure__caption">' . $caption . '</figcaption>';
    }

    $html = '<figure class="figure figure--' . esc_attr($size) . '">' . $html . '</figure>';

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
// TODO: only wp-quiz plugin requires jquery
add_action('wp_enqueue_scripts', function() {
    return false;

    if(!is_user_logged_in()) {
        wp_deregister_script('jquery');
    }
}, 11);


// Remove site icon controls from admin customizer
add_action('customize_register', function($wp_customize) {
     $wp_customize->remove_control('site_icon');
});


// Add search popover to footer
add_action('wp_footer', function() {
    get_search_form();
});


// Update annoying body classes
// @link https://github.com/WordPress/WordPress/blob/81500e50eff289e2f5601135707c22c03625a192/wp-includes/post-template.php#L590
add_filter('body_class', function($wp_classes, $extra_classes) {
    $classes = [];

    if(is_front_page())
        $classes[] = 'is-home';

    // TODO: remove after moving cards to post type #34
    if(is_singular('post') && !has_post_format('chat')) {
        $classes[] = 'is-post';
    }

    if(is_singular('page')) {
        $classes[] = 'is-page';
    }

    if(is_archive()) {
        $classes[] = 'is-archive';
    }

    if(is_admin_bar_showing()) {
        $classes[] = 'admin-bar';
    }

    return $classes;
}, 10, 2);


// Remove annoying post classes
add_filter('post_class', function($classes, $class) {
    $type = get_post_type();

    if(post_type_supports($type, 'post-formats')) {
        $type = get_post_format() ? : 'standard';
    }

    $class[] = 'post--' . $type;
    $class[] = 'js-mediator-article';

    return $class;
}, 10, 2);


// Change mail from fields
add_filter('wp_mail_from_name', function($name) {
    return __('knife.media', 'knife-theme');
});

add_filter('wp_mail_from', function($email) {
    $hostname = parse_url(site_url("/"), PHP_URL_HOST);

    return "no-reply@{$hostname}";
});


// It is good to remove auto suggestings for SEO
// https://core.trac.wordpress.org/ticket/16557
add_filter('redirect_canonical', function($url) {
    if(is_404() && !isset($_GET['p'])) {
        return false;
    }

    return $url;
});


// We don't want to use default gallery styles anymore
add_filter('use_default_gallery_style', '__return_false');


// For the reason that we don't use comments in this theme we have to remove comments feed link from header
add_filter('feed_links_show_comments_feed', '__return_false');


// Navigation links classes
add_filter('next_posts_link_attributes', function($atts) {
    return 'class="button"';
});

add_filter('previous_posts_link_attributes', function($atts) {
    return 'class="button"';
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


// Rename default posts format
add_filter('gettext_with_context', function($translation, $text, $context, $domain) {
    $names = [
        'Standard' => __('Стандартный', 'knife-theme'),
        'Aside'  => __('Без сайдбара', 'knife-theme'),
        'Video' => __('Видео', 'knife-theme'),
        'Audio' => __('Аудио', 'knife-theme'),
        'Gallery' => __('Галерея', 'knife-theme'),
        'Chat' => __('Карточки', 'knife-theme')
    ];

    if($context !== 'Post format') {
        return $translation;
    }

    return str_replace(array_keys($names), array_values($names), $text);
}, 10, 4);


// We need to wrap the content with card tag if it in chat post format
add_filter('the_content', function($content) {
    if(get_post_format() !== 'chat') {
        return $content;
    }

    if(has_shortcode($content, 'card')) {
        return $content;
    }

    return '<div class="post__card">' . $content . '</div>';
});


// Remove annoying [...] in excerpts
add_filter('excerpt_more', function($more) {
    return '&hellip;';
});


// Remove extra p from shortcodes
add_filter('the_content', function($content) {
    $array = [
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    ];

    return strtr($content, $array);
});


// Fix the_tags output
add_filter('the_tags', function($tags) {
    return str_replace('href="', 'class="tags__link" href="', $tags);
}, 10, 1);


// Remove useless image attributes
add_filter('post_thumbnail_html', function($html) {
    return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('get_image_tag', function($html) {
    return preg_replace('/(width|height)="\d*"\s/', "", $html);
}, 10);

add_filter('get_image_tag_class', function($class, $id, $align, $size) {
    $class = 'figure__image';

    return $class;
}, 0, 4);


// Disable post attachment pages
// Redirect to post parent if exists
add_action('template_redirect', function() {
    global $post;

    if(!is_attachment()) {
        return false;
    }

    if(isset($post->post_parent) && $post->post_parent > 0) {
        $url = get_permalink($post->post_parent);
    }
    else {
        $url = home_url('/');
    }

    wp_redirect(esc_url($url), 301);
    exit;
});

add_filter('attachment_link', function() {
    return;
});


// Disable wordpress based search to reduce CPU load and prevent DDOS attacks
add_action('parse_query', function($query) {
    if(!$query->is_search() || is_admin()) {
        return false;
    }

    $query->set('s', '');
    $query->is_search = false;
    $query->is_404 = true;
}, 9);


// Remove private posts from archives and home page.
// Note: Knife editors use private posts as drafts. So we don't want to see drafts in templates even if we have logged in
add_action('pre_get_posts', function($query) {
    if(is_admin() && $query->is_main_query()) {
        return;
    }

    if($query->is_archive() || $query->is_home()) {
        $query->set('post_status', 'publish');
    }
});


// Hide comments menu from admin menu
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});


// Add custom theme widgets from common hanlder
require get_template_directory() . '/core/modules/widget-handler.php';

// Theme filters
require get_template_directory() . '/core/modules/theme-filters.php';

// Upgrade theme menus
require get_template_directory() . '/core/modules/menu-upgrade.php';

// User generated blogs
require get_template_directory() . '/core/modules/user-club.php';

// News category
require get_template_directory() . '/core/modules/news-manager.php';

// Story post type
require get_template_directory() . '/core/modules/story-manager.php';

// Select post type
require get_template_directory() . '/core/modules/select-links.php';

// Special projects taxonomy settings
require get_template_directory() . '/core/modules/special-projects.php';

// Login screen custom styles
require get_template_directory() . '/core/modules/access-screen.php';

// Custom header meta for social networks and search engines
require get_template_directory() . '/core/modules/header-meta.php';

// Customize default wordpress embed code
require get_template_directory() . '/core/modules/embed-filters.php';

// Share buttons manager class
require get_template_directory() . '/core/modules/share-buttons.php';

// Return post info meta
require get_template_directory() . '/core/modules/post-info.php';

// Custom optional image for posts
require get_template_directory() . '/core/modules/post-sticker.php';

// Add second title to post
require get_template_directory() . '/core/modules/post-tagline.php';

// Primary post tag
require get_template_directory() . '/core/modules/primary-tag.php';

// Insert lead metabox to admin post screen
require get_template_directory() . '/core/modules/post-lead.php';

// Send push notifications service
require get_template_directory() . '/core/modules/push-service.php';

// Custom search engine settings
require get_template_directory() . '/core/modules/google-search.php';

// Custom background handler
require get_template_directory() . '/core/modules/custom-background.php';

// TinyMCE extending plugins
require get_template_directory() . '/core/modules/mce-plugins.php';

// Add custom Yandex.Zen feed template
require get_template_directory() . '/core/modules/yandex-zen.php';



// Custom template tags for this theme.
require get_template_directory() . '/core/helpers/template-tags.php';

// Custom theme shortcodes
require get_template_directory() . '/core/helpers/theme-shortcodes.php';

// Add plugins snippets
require get_template_directory() . '/core/helpers/plugin-snippets.php';

// Custom template filters for promo projects
require get_template_directory() . '/core/helpers/promo-filters.php';

