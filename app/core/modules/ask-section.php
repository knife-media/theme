<?php
/**
 * Ask post type
 *
 * Custom post type for user answers and questions
 *
 * @package knife-theme
 * @since 1.7
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Ask_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $slug = 'ask';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register ask post type
        add_action('init', [__CLASS__, 'register_type']);

        // Include ask single template
        add_action('single_template', [__CLASS__, 'include_single']);

        // Include ask archive template
        add_filter('archive_template', [__CLASS__, 'include_archive']);

        // Don't show empty archive
        add_action('template_redirect', [__CLASS__, 'redirect_empty_archive']);

        // Change posts count on ask archive
        add_action('pre_get_posts', [__CLASS__, 'update_count']);
    }


    /**
     * Register ask post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                    => [
                'name'                  => __('Вопросы', 'knife-theme'),
                'singular_name'         => __('Вопрос', 'knife-theme'),
                'add_new'               => __('Добавить вопрос', 'knife-theme'),
                'menu_name'             => __('Вопросы', 'knife-theme'),
                'all_items'             => __('Все вопросы', 'knife-theme'),
                'add_new_item'          => __('Добавить новый вопрос', 'knife-theme'),
                'new_item'              => __('Новый вопрос', 'knife-theme'),
                'edit_item'             => __('Редактировать вопросы', 'knife-theme'),
                'update_item'           => __('Обновить вопросы', 'knife-theme'),
                'view_item'             => __('Просмотреть вопрос', 'knife-theme'),
                'view_items'            => __('Просмотреть вопросы', 'knife-theme'),
                'search_items'          => __('Искать вопрос', 'knife-theme'),
                'insert_into_item'      => __('Добавить в вопрос', 'knife-theme')
            ],
            'label'                 => __('Вопросы', 'knife-theme'),
            'description'           => __('Раздел с вопросами пользователей', 'knife-theme'),
            'supports'              => ['title', 'editor', 'thumbnail', 'revisions', 'excerpt', 'author', 'comments'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 9,
            'menu_icon'             => 'dashicons-editor-help',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'taxonomies'            => ['post_tag']
        ]);
    }


    /**
     * Include archive ask template
     */
    public static function include_archive($template) {
        if(is_post_type_archive(self::$slug)) {
            $new_template = locate_template(['templates/archive-ask.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Include single ask template
     */
    public static function include_single($template) {
        if(is_singular(self::$slug)) {
            $new_template = locate_template(['templates/single-ask.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Redirect empty archive
     */
    public static function redirect_empty_archive() {
        if(is_post_type_archive(self::$slug) && !have_posts()) {
            wp_redirect(home_url(), 302);
            exit;
        }
    }


    /**
     * Change posts_per_page for ask archive template
     */
    public static function update_count($query) {
        if($query->is_main_query() && $query->is_post_type_archive(self::$slug)) {
            $query->set('posts_per_page', 12);
        }
    }
}


/**
 * Load current module environment
 */
Knife_Ask_Section::load_module();
