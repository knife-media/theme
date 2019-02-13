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
     * Unique nonce string
     *
     * @access  private
     * @var     string
     */
    private static $nonce = 'knife-ask-nonce';


    /**
     * Unique meta to store generator options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-ask-options';


    /**
     * Current question counter option
     * This meta stores in wp_options table
     *
     * @access  private
     * @var     string
     */
    private static $option_counter = 'knife-ask-counter';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register ask post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add ask author metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

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
     * Add ask author metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-ask-metabox', __('Настройка вопроса'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
    }


    /**
     * Display ask author metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/ask-metabox.php');
    }


    /**
     * Save ask author metabox
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            $options = $_REQUEST[self::$meta_options];

            if(isset($options['counter'])) {
                update_option(self::$option_counter, $options['counter']);
            }

            update_post_meta($post_id, self::$meta_options, $options);
        }
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
