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
    private static $post_meta = '_knife-authors';


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
             echo "<b>{$user->ID}:</b>{$user->display_name}\n";
        }

        wp_die();
    }
}


/**
 * Load current module environment
 */
Knife_Authors_Manager::load_module();
