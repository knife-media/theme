<?php
/**
 * Common theme filters
 *
 * Useful template manager class. Snippets from functions.php
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.16
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Theme_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Update theme roles and capabilites
        add_action('after_switch_theme', [__CLASS__, 'update_roles']);

        // Remove useless actions and settings
        add_action('after_setup_theme', [__CLASS__, 'remove_actions']);

        // Add widget size query var
        add_action('the_post', [__CLASS__, 'update_archive_item'], 10, 2);

        // Remove private posts from archives and home page
        add_action('pre_get_posts', [__CLASS__, 'remove_private_posts']);

        // Add excerpts for pages
        add_action('init', [__CLASS__, 'add_pages_excerpts']);

        // Remove longreads archive title
        add_filter('get_the_archive_title', [__CLASS__, 'remove_longreads_title']);

        // Update archive template title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title']);

        // Update archive template description
        add_filter('get_the_archive_description', [__CLASS__, 'update_archive_description']);

        // Update annoying body classes
        add_filter('body_class', [__CLASS__, 'update_body_classes'], 10, 2);

        // Update post classes
        add_filter('post_class', [__CLASS__, 'update_post_classes'], 10, 2);

        // Add post author link class
        add_filter('the_author_posts_link', [__CLASS__, 'add_author_class'], 10, 1);

        // Remove auto suggestions
        add_filter('do_redirect_guess_404_permalink', '__return_false');

        // Fix non-latin filenames
        add_action('sanitize_file_name', [__CLASS__, 'sanitize_file_name'], 12);

        // Preload styles and fonts
        add_action('wp_head', [__CLASS__, 'preload_assets'], 5);

        // Redirect old story post type to regular posts
        add_action('template_redirect', [__CLASS__, 'redirect_story'], 9);

        // Redirect old generator post type to regular posts
        add_action('template_redirect', [__CLASS__, 'redirect_generator'], 9);

        // Remove annoying [...] in excerpts
        add_filter('excerpt_more', function($more) {
            return '&hellip;';
        });

        // Change mail from fields
        add_filter('wp_mail_from_name', function($name) {
            return __('knife.media', 'knife-theme');
        });

        add_filter('wp_mail_from', function($email) {
            return "no-reply@knife.media";
        });

        // We don't need jquery for non logged in users
        add_action('wp_default_scripts', function($scripts) {
            if(!is_user_logged_in()) {
                $scripts->remove('jquery');
            }
        });

        add_action('pre_get_posts', function($query) {
            if(is_tax('label', 'editorial')) {
                $query->set('orderby', 'rand');
            }
        });

        add_action('term_description', function($description) {
            return wp_targeted_link_rel(links_add_target($description));
        }, 11);
    }


    /**
     * Preload styles and fonts in header
     *
     * @link https://web.dev/preload-critical-assets/
     * @since 1.14
     */
    public static function preload_assets() {
        $fonts = [
            'fonts/formular/formular-regular.woff2',
            'fonts/formular/formular-medium.woff2',
            'fonts/formular/formular-bold.woff2',
            'fonts/ptserif/ptserif-regular.woff2',
            'fonts/ptserif/ptserif-italic.woff2',
        ];

        foreach($fonts as $font) {
            printf(
                '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
                get_template_directory_uri() . '/assets/' . $font
            );
        }
    }


    /**
     * Update theme roles and capabilites
     *
     * @since 1.14
     */
    public static function update_roles() {
        // Update administrator capabilities
        $administrator = get_role('administrator');
        $administrator->add_cap('promo_manage');

        // Update editor capabilities
        $editor = get_role('editor');
        $editor->add_cap('edit_theme_options');
        $editor->add_cap('promo_manage');
        $editor->add_cap('edit_users');

        // Add new manager role
        add_role('manager', __('Менеджер', 'knife-theme'), [
            'read' => true,
            'promo_manage' => true,
            'upload_files' => true
        ]);
    }


    /**
     * Redirect old story post type to their regular ones
     *
     * @since 1.16
     */
    public static function redirect_story() {
        if(!is_404()) {
            return;
        }

        $slug = get_query_var('name');

        // Try to find story with this address
        $post = get_page_by_path('story-' . $slug, OBJECT, 'post');

        if(isset($post->ID) && $post->post_status === 'publish') {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }


    /**
     * Redirect old generator post type
     *
     * @since 1.16
     */
    public static function redirect_generator() {
        if(!is_404()) {
            return;
        }

        $slug = get_query_var('name');

        // Try to find post with this address
        $post = get_page_by_path($slug, OBJECT, 'post');

        if(isset($post->ID) && $post->post_status === 'publish') {
            wp_redirect(get_permalink($post->ID), 301);
            exit;
        }
    }


    /**
     * Fix non-latin file name chars
     *
     * Process right after https://github.com/antonlukin/rus-package
     *
     * @since 1.12
     */
    public static function sanitize_file_name($name) {
        return preg_replace('#[^a-z0-9.-_]#i', '-', $name);
    }


    /**
     * Add excerpts for pages
     *
     * @since 1.13
     */
    public static function add_pages_excerpts() {
        add_post_type_support('page', 'excerpt');
    }


    /**
     * Remove different WordPress actions right after theme setup
     *
     * @since 1.10
     */
    public static function remove_actions() {
        // Hide comments menu from admin menu
        add_action('admin_menu', function() {
            remove_menu_page('edit-comments.php');
        });

        // Remove admin bar styles from head
        add_action('get_header', function() {
            remove_action('wp_head', '_admin_bar_bump_cb');
        });

        // Remove useless widgets from wp-admin
        add_action('admin_init', function() {
            remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
            remove_meta_box('dashboard_primary', 'dashboard', 'normal');
        });

        // Remove comments and trackbacks metaboxes
        add_action('admin_init', function() {
            remove_meta_box('commentsdiv', 'post', 'normal');
            remove_meta_box('commentsdiv', 'page', 'normal');

            remove_meta_box('trackbacksdiv', 'post', 'normal');
            remove_meta_box('trackbacksdiv', 'page', 'normal');
        });

        // Remove fcking emojis and wordpress meta for security reasons
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        // Remove wp_head default actions
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'adjacent_posts_rel_link');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_resource_hints', 2);
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);

        // Remove unused HTTP headers
        remove_action('template_redirect', 'rest_output_link_header', 11);
        remove_action('template_redirect', 'wp_shortlink_header', 11);

        // Change wp_die errors status code for logged-in users
        add_filter('wp_die_handler', function($handler) {
            if(is_user_logged_in()) {
                $handler = function($message, $title, $args = []) {
                    $args['response'] = 200;

                    _default_wp_die_handler($message, $title, $args);
                };
            }

            return $handler;
        });

        // Disable REST API for anonymous users
        add_filter('rest_authentication_errors', function($result) {
            if(true === $result || is_wp_error($result)) {
                return $result;
            }

            if(!is_user_logged_in()) {
                return new WP_Error(
                    'rest_not_logged_in',
                    __('Authorization required for all API requests.'),
                    ['status' => 403]
                );
            }

            return $result;
        });
    }


    /**
     * Add post author link class
     *
     * @since 1.10
     */
    public static function add_author_class($link) {
        return str_replace('rel="author"', 'class="meta__item" rel="author"', $link);
    }


    /**
     * Print styles checkbox in posts
     *
     * @since 1.12
     */
    public static function print_styles_checkbox($template, $post) {
        if($post->post_type !== 'post') {
            return;
        }

        $form = get_post_meta($post->ID, 'dsf', true);

        printf(
            '<label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label>',
            esc_attr('sdf'),
            __('Добавить форму заявки в клуб', 'knife-theme'),
            checked($form, 1, false)
        );
    }


    /**
     * Set custom post classes using only post format
     *
     * @since 1.10
     */
    public static function update_post_classes($classes, $class) {
        return $class;
    }


    /**
     * Update annoying body classes
     *
     * @link https://github.com/WordPress/WordPress/blob/81500e50eff289e2f5601135707c22c03625a192/wp-includes/post-template.php#L590
     * @since 1.10
     */
    public static function update_body_classes($wp_classes, $extra_classes) {
        $classes = [];

        if(is_single()) {
            $classes[] = 'is-single';
        }

        if(is_archive()) {
            $classes[] = 'is-archive';
        }

        if(is_admin_bar_showing()) {
            $classes[] = 'is-adminbar';
        }

        if(is_front_page()) {
            $classes[] = 'is-front';
        }

        if(is_singular('page') && !is_front_page()) {
            $classes[] = 'is-page';
        }

        if(is_singular('post')) {
            $classes[] = 'is-post';
        }

        return $classes;
    }


    /**
     * Remove private posts from archives and home page.
     *
     * Note: Knife editors use private posts as drafts.
     * So we don't want to see drafts in templates even if we logged in
     *
     * @since 1.10
     */
    public static function remove_private_posts($query) {
        if(is_admin() || !$query->is_main_query()) {
            return;
        }

        if($query->is_archive() || $query->is_home()) {
            $query->set('post_status', 'publish');
        }
    }


    /**
     * Add widget size query var on archive loops
     */
    public static function update_archive_item($post, $query) {
        $size = function($current, $found) use (&$args) {
            if($found < 3 || $current % 5 === 3 || $current % 5 === 4) {
                return 'double';
            }

            return 'triple';
        };

        if(($query->is_archive() || $query->is_home()) && $query->is_main_query()) {
            set_query_var('widget_size', $size($query->current_post, (int) $query->found_posts));
        }
    }


    /**
     * Custom archive title
     */
    public static function update_archive_title($title) {
        if(is_category()) {
            return sprintf('<h1 class="caption__title caption__title--category">%s</h1>',
                single_term_title('', false)
            );
        }

        if(is_author()) {
            return sprintf('<h1 class="caption__title caption__title--author">%s</h1>',
                get_the_author()
            );
        }

        if(is_post_type_archive()) {
            return sprintf('<h1 class="caption__title">%s</h1>',
                post_type_archive_title('', false)
            );
        }

        if(is_tag() || is_tax()) {
            return sprintf('<h1 class="caption__title">%s</h1>',
                single_term_title('', false)
            );
        }

        return sprintf('<h1 class="caption__title">%s</h1>', $title);
    }


    /**
     * Remove archive title for longreads
     *
     * @since 1.11
     */
    public static function remove_longreads_title($title) {
        if(is_category('longreads')) {
            return '';
        }

        return $title;
    }


    /**
     * Custom archive description
     */
    public static function update_archive_description($description) {
        if(is_author()) {
            $description = get_the_author_meta('description');
        }

        if(!empty($description)) {
            $description = sprintf('<div class="caption__description">%s</div>',
                wpautop($description)
            );
        }

        return $description;
    }
}


/**
 * Load current module environment
 */
Knife_Theme_Filters::load_module();

