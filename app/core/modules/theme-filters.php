<?php
/**
 * Common theme filters
 *
 * Useful template manager class. Snippets from functions.php
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.12
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Theme_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Remove useless actions and settings
        add_action('after_setup_theme', [__CLASS__, 'remove_actions']);

        // Add widget size query var
        add_action('the_post', [__CLASS__, 'update_archive_item'], 10, 2);

        // Remove longreads archive title
        add_action('wp', [__CLASS__, 'remove_longreads_title']);

        // Remove private posts from archives and home page
        add_action('pre_get_posts', [__CLASS__, 'remove_private_posts']);


        // Update archive template title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title']);

        // Update archive template description
        add_filter('get_the_archive_description', [__CLASS__, 'update_archive_description']);

        // Update annoying body classes
        add_filter('body_class', [__CLASS__, 'update_body_classes'], 10, 2);

        // Update post classes
        add_filter('post_class', [__CLASS__, 'update_post_classes'], 10, 2);

        // Rename default posts format
        add_filter('gettext_with_context', [__CLASS__, 'rename_post_formats'], 10, 4);

        // Add post author link class
        add_filter('the_author_posts_link', [__CLASS__, 'add_author_class'], 10, 1);

        // Remove auto suggestions
        add_filter('redirect_canonical', [__CLASS__, 'remove_autosuggest']);

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
    }


    /**
     * It is good to remove auto suggestings for SEO
     *
     * @link https://core.trac.wordpress.org/ticket/16557
     * @since 1.10
     */
    public static function remove_autosuggest($url) {
        if(is_404() && !isset($_GET['p'])) {
            return false;
        }

        return $url;
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
     * Rename default posts formats
     *
     * @since 1.10
     */
    public static function rename_post_formats($translation, $text, $context, $domain) {
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
    public static function remove_longreads_title() {
        if(is_category('longreads')) {
            add_action('get_the_archive_title', '__return_empty_string');
        }
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

