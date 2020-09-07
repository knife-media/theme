<?php
/**
 * Guest authors manager
 *
 * Allow mutiple guest authors per post
 *
 * @package knife-theme
 * @since 1.11
 * @version 1.14
 */

if (!defined('WPINC')) {
    die;
}


class Knife_Authors_Manager {
    /**
     * Post meta to store authors array
     *
     * @access  public
     * @var     string
     */
    public static $meta_authors = '_knife-authors';


    /**
     * Post meta to store editor value
     *
     * @access  public
     * @var     string
     * @since   1.14
     */
    public static $meta_editor = '_knife-editor';


    /**
     * Default post type lead text availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'select', 'generator', 'quiz'];


    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_action = 'knife-authors-search';


    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-authors-nonce';


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
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'append_author']);

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

        // Hooks to modify the published post number count on the Users List Table
        add_filter('manage_users_columns', [__CLASS__, 'filter_users_columns']);
        add_filter('manage_users_custom_column', [__CLASS__, 'filter_users_custom_column'], 10, 3);

        // Remove email column on the Users List Table
        add_filter('manage_users_columns', [__CLASS__, 'remove_email_column']);

        // Show corrent user posts counter
        add_filter('get_usernumposts', [__CLASS__, 'count_user_posts'], 10, 2);

        add_action('user_profile_update_errors', function($errors) {
            $errors->remove('empty_email');
        }, 10, 3);

        add_filter('pre_user_email', function($email) {
            if(empty($email)) {
                $email = uniqid() . "@knife.support";
            }

            return $email;
        });
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
            [__CLASS__, 'display_metabox'],
            self::$post_type, 'side'
        );
    }


    /**
     * Save authors post meta
     */
    public static function save_metabox($post_id, $post) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        delete_post_meta($post_id, self::$meta_editor);

        // Add editors post meta
        if(array_key_exists($_REQUEST[self::$meta_editor], self::get_editors())) {
            add_post_meta($post_id, self::$meta_editor, $_REQUEST[self::$meta_editor]);
        }

        // Delete all authors values
        delete_post_meta($post_id, self::$meta_authors);

        // Add post author if empty array
        if(empty($_REQUEST[self::$meta_authors])) {
            return add_post_meta($post_id, self::$meta_authors, $post->post_author);
        }

        $authors = (array) $_REQUEST[self::$meta_authors];

        // Add authors array from metabox input
        foreach($authors as $author) {
            add_post_meta($post_id, self::$meta_authors, absint($author));
        }
    }


    /**
     * Enqueue admin side assets
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Add assets for metabox only
        if(in_array($hook, ['post.php', 'post-new.php'])) {
            // Auto suggest
            wp_enqueue_script('suggest');

            // Insert metabox styles
            wp_enqueue_style('knife-authors-metabox', $include . '/styles/authors-metabox.css', [], $version);

            // Insert metabox scripts
            wp_enqueue_script('knife-authors-metabox', $include . '/scripts/authors-metabox.js', ['jquery'], $version);

            $options = [
                'post_meta' => esc_attr(self::$meta_authors),
                'action' => esc_attr(self::$ajax_action),
                'nonce' => wp_create_nonce(self::$ajax_nonce),

                'error' => __('Непредвиденная ошибка сервера', 'knife-theme'),
                'verify' => __('Создать нового гостевого автора?', 'knife-theme')
            ];

            wp_localize_script('knife-authors-metabox', 'knife_authors_metabox', $options);
        }

        // Insert common styles
        wp_enqueue_style('knife-authors-common', $include . '/styles/authors-common.css', [], $version);

        // Add assets for user screen only
        if(in_array($hook, ['user-edit.php', 'user-new.php'])) {
            wp_enqueue_script('knife-authors-user', $include . '/scripts/authors-user.js', ['jquery'], $version);
        }
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
    public static function append_author() {
        if(isset($_REQUEST['q'])) {
            return self::suggest_authors($_REQUEST['q']);
        }

        check_admin_referer(self::$ajax_nonce, 'nonce');

        // Get author field
        $author = sanitize_text_field(trim($_REQUEST['author']));

        // Parse fisrt and last names
        list($first, $last) = explode(' ', $author, 2);

        // Sanitize login
        $login = sanitize_title($author);

        // Try to find duplicate login
        if(get_user_by('login', $login)) {
            wp_send_json_error(__('Пользователь с таким логином уже существует', 'knife-theme'));
        }

        $args = [
            'user_login' => $login,
            'user_nicename' => $login,
            'display_name' => $author,
            'first_name' => $first,
            'last_name' => $last,
            'role' => 'subscriber',
            'user_pass' => wp_generate_password()
        ];

        // Create new user
        $user_id = wp_insert_user($args);

        if(is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
        }

        wp_send_json_success(['id' => $user_id, 'author' => $author]);
    }


    /**
     * Replace author field for feeds
     */
    public static function replace_feed_author($name) {
        if(is_feed() && !is_admin()) {
            $authors = get_post_meta(get_the_ID(), self::$meta_authors);

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
                'key' => self::$meta_authors,
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
                $new_columns['authors-name'] = __('Авторы', 'knife-theme');
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
        if($column === 'authors-name') {
            global $post;

            // Get authors
            $authors = get_post_meta($post->ID, self::$meta_authors);

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
     * Unset email column from users table
     */
    public static function remove_email_column($columns) {
        unset($columns['email']);

        return $columns;
    }


    /**
     * Unset the post count column because it's going to be inaccurate and provide our own
     */
    public static function filter_users_columns($columns) {
        $new_columns = [];

        // Unset and add our column while retaining the order of the columns
        foreach($columns as $name => $title) {
            if($name === 'posts') {
                $new_columns['authors-count'] = __('Записи', 'knife-theme');
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
        if($column !== 'authors-count') {
            return $output;
        }

        // We filter count_user_posts() so it provides an accurate number
        $count = (int) count_user_posts($user_id);

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
        global $wpdb;

        // Try to get cached value
        $counts = wp_cache_get($user_id, 'knife-user-posts');

        if($counts !== false) {
            return $counts;
        }

        $query = "SELECT COUNT(*) as counts FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d";
        $results = $wpdb->get_row($wpdb->prepare($query, self::$meta_authors, $user_id));

        // Retrieve counts
        $counts = $results->counts;

        // Set cache on 12 hours
        wp_cache_set($user_id, $counts, 'knife-user-posts', 12 * HOUR_IN_SECONDS);

        return $counts;
    }


    /**
     * Return reserverd editors list
     */
    public static function get_editors() {
        $editors = [
            'artem-chapaev' => 'Артем Чапаев',
            'nastya-travkina' => 'Настя Травкина',
            'seroe-fioletovoe' => 'Серое Фиолетовое',
            'fragments' => 'Фрагменты',
            'tania-cohen' => 'Таня Коэн',
            'ivan' => 'Ваня'
        ];

        return $editors;
    }


    /**
     * Suggest authors on ajax request
     */
    private static function suggest_authors($query) {
        $search = sanitize_text_field(strtolower($query));

        $args = [
            'number' => 10,
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
            printf("<i>%d:</i>%s\n", $user->ID, esc_html($user->display_name));
        }

        if(count($users) < 1) {
            printf("<i>+</i>%s<b>%s</b>\n", __('Создать:', 'knife-theme'), $search);
        }

        wp_die();
    }
}


/**
 * Load current module environment
 */
Knife_Authors_Manager::load_module();
