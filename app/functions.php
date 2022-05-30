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
 * @version 1.15
 */


/**
 * We have to install this value
 */
if(!isset($content_width)) {
    $content_width = 1024;
}


/**
 * Insert required js files
 */
add_action('wp_enqueue_scripts', function() {
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    wp_enqueue_script('knife-theme', get_template_directory_uri() . '/assets/scripts.min.js', [], $version, true);
});


/**
 * Insert styles
 */
add_action('wp_print_styles', function() {
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    wp_enqueue_style('knife-theme', get_template_directory_uri() . '/assets/styles.min.css', [], $version);
});


/**
 * Rewrite urls after switch theme just in case
 */
add_action('after_switch_theme', function() {
     flush_rewrite_rules();
});


/**
 * Add required theme support tags
 */
add_action('after_setup_theme', function() {
    // Let wordpress generate page title
    add_theme_support('title-tag');

    // Add links to feeds in header
    add_theme_support('automatic-feed-links');

    // Let wordpress manage cutsom background
    add_theme_support('custom-background', ['wp-head-callback' => '__return_false']);
});


/**
 * Add editor custom styles
 */
add_action('admin_enqueue_scripts', function() {
    $version = wp_get_theme()->get('Version');

    // Insert custom editor styles
    add_editor_style('core/include/styles/editor-styles.css', [], $version);
});


/**
 * Disable Aggressive Updates
 *
 * @link https://wp-kama.ru/id_8514/uskoryaem-adminku-wordpress-otklyuchaem-proverki-obnovlenij.html
 */
if(is_admin()) {
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');

    remove_action('load-plugins.php', 'wp_update_plugins');
    remove_action('load-themes.php', 'wp_update_themes');

    add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_empty_array');
}



// Theme filters
require get_template_directory() . '/core/modules/theme-filters.php';

// Content filters
require get_template_directory() . '/core/modules/content-filters.php';

// ID handler
require get_template_directory() . '/core/modules/id-handler.php';

// Add custom theme widgets from common hanlder
require get_template_directory() . '/core/modules/widget-handler.php';

// Widget visibility handler
require get_template_directory() . '/core/modules/hidden-widgets.php';

// Add invisible post status
require get_template_directory() . '/core/modules/invisible-posts.php';

// Allow mutiple guest authors per post
require get_template_directory() . '/core/modules/authors-manager.php';

// Upgrade theme menus
require get_template_directory() . '/core/modules/menu-upgrade.php';

// User generated blogs
require get_template_directory() . '/core/modules/club-section.php';

// News category
require get_template_directory() . '/core/modules/news-manager.php';

// Quiz post type
require get_template_directory() . '/core/modules/quiz-section.php';

// Generator post type
require get_template_directory() . '/core/modules/generator-section.php';

// Best posts markup
require get_template_directory() . '/core/modules/best-posts.php';

// Translate posts markup
require get_template_directory() . '/core/modules/translations-posts.php';

// Special projects taxonomy settings
require get_template_directory() . '/core/modules/special-projects.php';

// Login screen custom styles
require get_template_directory() . '/core/modules/access-screen.php';

// Custom metabox for SEO fields
require get_template_directory() . '/core/modules/seo-fields.php';

// Custom site meta and footer description
require get_template_directory() . '/core/modules/site-meta.php';

// Add custom user fields
require get_template_directory() . '/core/modules/user-meta.php';

// Promo posts classification with custom possibility
require get_template_directory() . '/core/modules/promo-manager.php';

// Required admin-side image filters
require get_template_directory() . '/core/modules/image-filters.php';

// Customize default wordpress embed code
require get_template_directory() . '/core/modules/embed-filters.php';

// Poster templates options
require get_template_directory() . '/core/modules/poster-templates.php';

// Create sharing image for social networks
require get_template_directory() . '/core/modules/snippet-image.php';

// Share buttons manager class
require get_template_directory() . '/core/modules/share-buttons.php';

// Return post info meta
require get_template_directory() . '/core/modules/post-info.php';

// Add second title to post
require get_template_directory() . '/core/modules/post-tagline.php';

// Primary post tag
require get_template_directory() . '/core/modules/primary-tag.php';

// Insert lead metabox to admin post screen
require get_template_directory() . '/core/modules/post-lead.php';

// Mark adult content posts
require get_template_directory() . '/core/modules/adult-content.php';

// Mark momentary posts
require get_template_directory() . '/core/modules/momentary-posts.php';

// Custom search engine settings
require get_template_directory() . '/core/modules/google-search.php';

// Enable terms emojis for default and custom taxonomies
require get_template_directory() . '/core/modules/terms-emoji.php';

// Show similar posts grouped by common tags inside single template
require get_template_directory() . '/core/modules/similar-posts.php';

// Custom background handler
require get_template_directory() . '/core/modules/custom-background.php';

// TinyMCE extending plugins
require get_template_directory() . '/core/modules/mce-plugins.php';

// Add custom feeds
require get_template_directory() . '/core/modules/extra-feeds.php';

// Return short links manager with simple stat
require get_template_directory() . '/core/modules/short-manager.php';

// Collect and display post analytics
require get_template_directory() . '/core/modules/analytics-manager.php';

// Send typo errors to telegram private channel
require get_template_directory() . '/core/modules/mistype-reporter.php';


// Custom template tags for this theme.
require get_template_directory() . '/core/helpers/template-tags.php';

// Add plugins snippets
require get_template_directory() . '/core/helpers/plugin-snippets.php';

// Custom template filters for promo projects
require get_template_directory() . '/core/helpers/promo-filters.php';
