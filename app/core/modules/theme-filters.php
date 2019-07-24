<?php
/**
 * Common theme filters
 *
 * Useful template manager class
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.10
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Theme_Filters {
    /**
     * Global widget exclude option
     *
     * @since   1.10
     * @access  private
     * @var     string
     */
    private static $exclude_option = 'knife_widget_exclude';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add widget size query var
        add_action('the_post', [__CLASS__, 'update_archive_item'], 10, 2);

        // Update archive template title
        add_action('get_the_archive_title', [__CLASS__, 'update_archive_title']);

        // Update archive template description
        add_action('get_the_archive_description', [__CLASS__, 'update_archive_description']);

        // Update exclude option after frontal widebar loaded
        add_action('dynamic_sidebar_after', [__CLASS__, 'update_exclude_option']);

        // Remove private posts from archives and home page
        add_action('pre_get_posts', [__CLASS__, 'remove_private_posts']);

        // Exclude frontpage posts from home archive
        add_action('pre_get_posts', [__CLASS__, 'clear_home_archive']);
    }


    /**
     * Remove private posts from archives and home page.
     *
     * Note: Knife editors use private posts as drafts.
     * So we don't want to see drafts in templates even if we logged in
     *
     * @since 1.10
     */
    public static function clear_home_archive($query) {
        if(is_admin() || !$query->is_main_query()) {
            return;
        }

        if($query->is_home()) {
            $exclude = get_option(self::$exclude_option, []);

            if(!empty($exclude)) {
                $query->set('post__not_in', $exclude);
            }
        }
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
     * Update exclude option after frontal widebar loaded
     *
     * @since 1.10
     */
    public static function update_exclude_option($id) {
        if($id === 'knife-frontal') {
            $exclude = get_query_var('widget_exclude', []);

            if(!empty($exclude)) {
                update_option(self::$exclude_option, $exclude);
            }
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
            return sprintf('<h1 class="tagline-title tagline-title--category">%s</h1>',
                single_term_title('', false)
            );
        }

        if(is_author()) {
            return sprintf('<h1 class="tagline-title tagline-title--author">%s</h1>',
                get_the_author()
            );
        }

        if(is_post_type_archive()) {
            return sprintf('<h1 class="tagline-title">%s</h1>',
                post_type_archive_title('', false)
            );
        }

        if(is_tag() || is_tax()) {
            return sprintf('<h1 class="tagline-title">%s</h1>',
                single_term_title('', false)
            );
        }

        return sprintf('<h1 class="tagline-title">%s</h1>', $title);
    }


    /**
     * Custom archive description
     */
    public static function update_archive_description($description) {
        if(is_author()) {
            $description = get_the_author_meta('description');
        }

        if(!empty($description)) {
            $description = sprintf('<div class="tagline-description">%s</div>',
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

