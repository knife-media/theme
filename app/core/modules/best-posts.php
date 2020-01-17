<?php
/**
 * Best posts
 *
 * Custom post meta to mark best posts
 *
 * @package knife-theme
 * @since 1.11
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Best_Posts {
    /**
     * Post meta to pass best option
     *
     * @access  public
     * @var     string
     */
    public static $meta_best = '_knife-best-post';


    /**
     * Default post type with posts labels
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'quiz'];


    /**
     * Archive query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'best';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Create custom best archive url
        add_action('init', [__CLASS__, 'add_best_rule']);

        // Update query_vars for best archive
        add_action('parse_query', [__CLASS__, 'update_query_vars']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_best_var']);

        // Include archive template for best posts
        add_filter('template_include', [__CLASS__, 'include_archive']);

        // Show posts only with best post meta
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Update best archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_document_title'], 10);

        // Add setting to set best post meta
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox']);

        // Update best post meta
        add_action('save_post', [__CLASS__, 'save_meta']);
    }


    /**
     * Create custom best archive url
     */
    public static function add_best_rule() {
        add_rewrite_rule(
            sprintf('^%s/?$', self::$query_var),
            sprintf('index.php?%s=1', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/page/([0-9]+)/?$', self::$query_var),
            sprintf('index.php?%s=1&paged=$matches[1]', self::$query_var),
            'top'
        );
    }


    /**
     * Update query_vars for best archive
     */
    public static function update_query_vars() {
        global $wp_query;

        if(array_key_exists(self::$query_var, $wp_query->query_vars)) {
            $wp_query->is_archive = true;
            $wp_query->is_home = false;
        }
    }


    /**
     * Append best query tag to availible query vars
     */
    public static function append_best_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include archive.php template for best posts
     */
    public static function include_archive($template) {
        if(get_query_var(self::$query_var)) {
            $new_template = locate_template(['archive.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Show only best posts in custom archive template
     */
    public static function update_query($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        if(get_query_var(self::$query_var)) {
            $query->set('post_type', self::$post_type);
            $query->set('orderby', 'rand');

            $query->set('meta_key', self::$meta_best);
            $query->set('meta_value', 1);
        }
    }


    /**
     * Update post archive caption title
     */
    public static function update_archive_title($title) {
        if(get_query_var(self::$query_var)) {
            $title = sprintf('<h1 class="caption__title">%s</h1>',
                __('Выбор редакции', 'knife-theme')
            );
        }

        return $title;
    }


    /**
     * Update best archive document title
     */
    public static function update_document_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Выбор редакции', 'knife-theme');
        }

        return $title;
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(!in_array(get_post_type($post->ID), self::$post_type)) {
            return;
        }

        $best = get_post_meta($post->ID, self::$meta_best, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_best),
            __('Выбор редакции', 'knife-theme'),
            checked($best, 1, false)
        );
    }


    /**
     * Save best term for post
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_best])) {
            return delete_post_meta($post_id, self::$meta_best);
        }

        return update_post_meta($post_id, self::$meta_best, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Best_Posts::load_module();
