<?php
/**
 * Guest authors manager
 *
 * Allow mutiple guest authors per post
 *
 * @package knife-theme
 * @since 1.11
 */

if (!defined('WPINC')) {
    die;
}


class Knife_Authors_Manager {
    /**
     * Post meta to store authors array
     *
     * @access  private
     * @var     string
     */
    public static $post_meta = '_knife-authors';


    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_action = 'knife-authors-search';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add guest authors metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save authors post meta
        add_action('save_post', [__CLASS__, 'save_metabox'], 10, 2);

        // Enqueue post metabox scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Remove default author metabox
        add_action('add_meta_boxes', [__CLASS__, 'remove_authors_box']);

        // Suggest authors on metabox input
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'suggest_authors']);

        // Show custom author in feeds
        add_filter('the_author', [__CLASS__, 'replace_feed_author']);

        // Fix author query
        add_action('parse_query', [__CLASS__, 'fix_author_posts']);

        // Update author data
        add_action('wp', [__CLASS__, 'update_authordata']);

        // Hooks to update posts author column
        add_filter('manage_posts_columns', [__CLASS__, 'filter_posts_columns']);
        add_filter('manage_pages_columns', [__CLASS__, 'filter_posts_columns']);
        add_action('manage_posts_custom_column', [__CLASS__, 'filter_posts_custom_column']);
        add_action('manage_pages_custom_column', [__CLASS__, 'filter_posts_custom_column']);

        // Hooks to modify the published post number count on the Users WP List Table
        add_filter('manage_users_columns', [__CLASS__, 'filter_users_columns']);
        add_filter('manage_users_custom_column', [__CLASS__, 'filter_users_custom_column'], 10, 3);

        // Show corrent user posts counter
        add_filter('get_usernumposts', [__CLASS__, 'count_user_posts'], 10, 2);
    }


    /**
     * Update author data for author archive
     *
     * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp.php#L595
     */
    public static function update_authordata() {
        global $authordata;

        if(is_author()) {
            $authordata = get_userdata(get_queried_object_id());
        }
    }


    /**
     * Add author metabox for editors
     */
    public static function add_metabox() {
        add_meta_box('knife-authors-metabox',
            __('Авторы', 'knife-theme'),
            [__CLASS__, 'display_metabox'], get_post_type(), 'side'
        );
    }


    /**
     * Save authors post meta
     */
    public static function save_metabox($post_id, $post) {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Delete all authors values
        delete_post_meta($post_id, self::$post_meta);

        // Add post author if empty array
        if(empty($_REQUEST[self::$post_meta])) {
            return add_post_meta($post_id, self::$post_meta, $post->post_author);
        }

        $authors = (array) $_REQUEST[self::$post_meta];

        // Add authors array from metabox input
        foreach($authors as $author) {
            add_post_meta($post_id, self::$post_meta, absint($author));
        }
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        $post_id = get_the_ID();

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Auto suggest
        wp_enqueue_script('suggest');

        // Insert admin styles
        wp_enqueue_style('knife-authors-metabox', $include . '/styles/authors-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-authors-metabox', $include . '/scripts/authors-metabox.js', ['jquery'], $version);

        $options = [
            'post_meta' => esc_attr(self::$post_meta),
            'action' => esc_attr(self::$ajax_action),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-authors-metabox', 'knife_authors_metabox', $options);
    }


    /**
     * Display authors metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/authors-metabox.php');
    }


    /**
     * Removes the standard WordPress 'Author' box.
     */
    public static function remove_authors_box() {
        remove_meta_box('authordiv', get_post_type(), 'normal');
    }


    /**
     * Suggest authors on metabox input
     */
    public static function suggest_authors() {
        $search = sanitize_text_field(strtolower($_REQUEST['q']));

        $args = [
            'number' => 20,
            'fields' => ['ID', 'display_name'],
            'search' => sprintf('*%s*', $search),
            'search_columns' => [
                'ID',
                'display_name',
                'user_email',
                'user_login',
            ]
        ];

        $users = get_users($args);

        foreach($users as $user) {
            printf("<b>%d:</b>%s\n", $user->ID, esc_html($user->display_name));
        }

        wp_die();
    }


    /**
     * Replace author field for feeds
     */
    public static function replace_feed_author($name) {
        if(is_feed() && !is_admin()) {
            $authors = get_post_meta(get_the_ID(), self::$post_meta);

            if(!empty($authors)) {
                $output = [];

                foreach($authors as $author) {
                    $user = get_userdata($author);

                    // Add display name to data
                    $output[] = esc_html($user->display_name);
                }

                $name = implode(', ', $output);
            }
        }

        return $name;
    }


    /**
     * Fix post list by author
     */
    public static function fix_author_posts($query) {
        if($query->is_author()) {
            $author = $query->get('author');

            if(empty($author)) {
                $user = get_user_by('slug', $query->get('author_name'));

                // Set id by author name
                if(isset($user->ID)) {
                    $author = $user->ID;
                }

                $query->set('author_name', null);
            }

            // Set meta query based on meta
            $query->set('meta_query', [[
                'key' => self::$post_meta,
                'value' => absint($author)
            ]]);

            $query->set('author', null);

            // Update queried object
            $query->queried_object_id = $author;
            $query->queried_object = get_userdata($author);
        }
    }


    /**
     * Add guest authors column to post lists
     */
    public static function filter_posts_columns($columns) {
        $new_columns = [];

        foreach($columns as $key => $value ) {
            $new_columns[$key] = $value;

            if($key === 'title') {
                $new_columns['guestauthor'] = __('Авторы', 'knife-theme');
            }

            if($key === 'author') {
                unset($new_columns[$key]);
            }
        }

        return $new_columns;
    }


    /**
     * Add custom authors to 'authors' column on edit pages
     */
    public static function filter_posts_custom_column($column) {
        if($column === 'guestauthor') {
            global $post;

            // Get authors
            $authors = get_post_meta($post->ID, self::$post_meta);

            if(empty($authors)) {
                $authors[] = $post->post_author;
            }

            $output = [];

            foreach($authors as $author) {
                $user = get_userdata($author);

                // Create args array for url composing
                $args = [
                    'post_type' => $post->post_type,
                    'author' => $user->ID
                ];

                $output[] = sprintf('<a href="%s">%s</a>',
                    esc_url(add_query_arg($args, admin_url('edit.php'))),
                    esc_html($user->display_name)
                );
            }

            echo implode(', ', $output);
        }
    }


    /**
     * Unset the post count column because it's going to be inaccurate and provide our own
     */
    public static function filter_users_columns($columns) {
        $new_columns = [];

        // Unset and add our column while retaining the order of the columns
        foreach($columns as $name => $title) {
            if($name === 'posts') {
                $new_columns['guestauthor'] = __('Записи', 'knife-theme');
                continue;
            }

            $new_columns[$name] = $title;
        }

        return $new_columns;
    }


    /**
     * Provide an accurate count when looking up the number of published posts for a user
     */
    public static function filter_users_custom_column($output, $column, $user_id) {
        if($column !== 'guestauthor') {
            return $output;
        }

        // We filter count_user_posts() so it provides an accurate number
        $count = count_user_posts($user_id);

        if($count === 0) {
            return '0';
        }

        $value = sprintf('<a href="%s" class="edit">%d</a>',
            esc_url(add_query_arg(['author' => $user_id], admin_url('edit.php'))),
            absint($count)
        );

        return $output . $value;
    }



    /**
     * Filter the count_users_posts() core function to include our correct count.
     */
    public static function count_user_posts($count, $user_id) {
        $query = new WP_Query([
            'meta_key' => self::$post_meta,
            'meta_value' => $user_id
        ]);

        return $query->found_posts;
    }
}


/**
 * Load current module environment
 */
Knife_Authors_Manager::load_module();
