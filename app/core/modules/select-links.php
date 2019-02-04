<?php
/**
 * Selection type
 *
 * Custom post type for manual articles select
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.7
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Select_Links {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $slug = 'select';


    /**
     * Unique nonce string using for ajax referer check
     *
     * @access  private
     * @var     string
     */
    private static $nonce = 'knife-select-nonce';


   /**
    * Ajax action
    *
    * @access  private
    * @var     string
    */
    private static $action = 'knife-select-title';


    /**
     * Unique meta using for saving post data
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-select-items';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register select post type
        add_action('init', [__CLASS__, 'register_type']);

        // Include select single template
        add_action('single_template', [__CLASS__, 'include_single']);

        // Include select archive template
        add_filter('archive_template', [__CLASS__, 'include_archive']);

        // Don't show empty archive
        add_action('template_redirect', [__CLASS__, 'redirect_empty_archive']);

        // Add select metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Get postid by url
        add_action('wp_ajax_' . self::$action, [__CLASS__, 'get_post_by_url']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Change posts count on select archive
        add_action('pre_get_posts', [__CLASS__, 'update_count']);

        // Update title with strong number
        add_filter('the_title', [__CLASS__, 'select_title'], 10, 2);

        // Filter content to show custom links
        add_filter('the_content', [__CLASS__, 'update_content']);
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                    => [
                'name'                  => __('Подборки', 'knife-theme'),
                'singular_name'         => __('Подборка', 'knife-theme'),
                'add_new'               => __('Добавить подборку', 'knife-theme'),
                'menu_name'             => __('Подборки', 'knife-theme'),
                'all_items'             => __('Все подборки', 'knife-theme'),
                'add_new_item'          => __('Добавить новую подборку', 'knife-theme'),
                'new_item'              => __('Новая подборка', 'knife-theme'),
                'edit_item'             => __('Редактировать подборку', 'knife-theme'),
                'update_item'           => __('Обновить подборку', 'knife-theme'),
                'view_item'             => __('Просмотреть подборку', 'knife-theme'),
                'view_items'            => __('Просмотреть подборки', 'knife-theme'),
                'search_items'          => __('Искать подборку', 'knife-theme'),
                'insert_into_item'      => __('Добавить в подборку', 'knife-theme')
            ],
            'label'                 => __('Подборка', 'knife-theme'),
            'description'           => __('Читай лучшее в подборках на Ноже', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author', 'comments'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 7,
            'menu_icon'             => 'dashicons-images-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Include archive story template
     *
     * @since 1.5
     */
    public static function include_archive($template) {
        if(is_post_type_archive(self::$slug)) {
            $new_template = locate_template(['templates/archive-select.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Include single select template
     */
    public static function include_single($template) {
        if(is_singular(self::$slug)) {
            $new_template = locate_template(['templates/single-select.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Redirect empty archive
     *
     * @since 1.6
     */
    public static function redirect_empty_archive() {
        if(is_post_type_archive(self::$slug) && !have_posts()) {
            wp_redirect(home_url(), 302);
            exit;
        }
    }


    /**
     * Filter the select title
     *
     * @since 1.5
     */
    public static function select_title($title, $post_id) {
        if(!is_admin()) {
            $words = explode(' ', ltrim($title));

            if(is_numeric($words[0])) {
                $title = "<strong>{$words[0]}</strong> " . implode(' ', array_slice($words, 1));
            }
        }

        return $title;
    }


    /**
     * Get postid by url using admin side ajax
     */
    public static function get_post_by_url() {
        check_admin_referer(self::$nonce, 'nonce');

        if(isset($_POST['link'])) {
            $link = sanitize_text_field($_POST['link']);

            // Get post id by url
            $post_id = url_to_postid($link);

            if($post_id > 0) {
                $data = [
                    'title' => get_the_title($post_id),
                    'poster' => strval(get_the_post_thumbnail_url($post_id)),
                    'attachment' => get_post_thumbnail_id($post_id)
                ];

                wp_send_json_success($data);
            }
        }

        wp_send_json_error();
    }


    /**
     * Update content with custom links
     */
    public static function update_content($content) {
        if(is_singular(self::$slug) && in_the_loop()) {
            $units = get_post_meta(get_the_ID(), self::$meta_items);

            foreach($units as $unit) {
                $content = self::append_unit($unit, $content);
            }
        }

        return $content;
    }


    /**
     * Change posts_per_page for select archive template
     *
     * @since 1.6
     */
    public static function update_count($query) {
        if($query->is_main_query() && $query->is_post_type_archive(self::$slug)) {
            $query->set('posts_per_page', 24);
        }
    }


    /**
     * Add select metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-select-metabox', __('Подборка статей'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-select-metabox', $include . '/styles/select-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-select-metabox', $include . '/scripts/select-metabox.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'action' => esc_attr(self::$action),
            'nonce' => wp_create_nonce(self::$nonce),
            'choose' => __('Выберите изображение постера', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-select-metabox', 'knife_select_metabox', $options);
    }


    /**
     * Display select link metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/select-metabox.php');
    }


    /**
     * Save post options
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

        // Update items meta
        self::update_items(self::$meta_items, $post_id);
    }


    /**
     * Update select items meta from post-metabox
     */
    private static function update_items($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete select post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            foreach($item as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i])) {
                    $i++;
                }

                if(!empty($value)) {
                    $meta[$i][$key] = sanitize_text_field($value);
                }
            }
        }

        foreach($meta as $item) {
            add_post_meta($post_id, $query, $item);
        }
    }


    /**
     * Get select unit from meta
     */
    private static function append_unit($attributes, $content = '') {
        $required = ['attachment', 'link', 'title'];

        // Not append unit if at least one required attribute is empty
        if(array_diff_key(array_flip($required), $attributes)) {
            return $content;
        }

        $unit = sprintf(
            '<div class="unit"><div class="unit__inner">%s</div></div>',

            sprintf(
                '<div class="unit__image">%s</div><div class="unit__content">%s</div>',

                wp_get_attachment_image($attributes['attachment'], 'double', false,
                    ['class' => 'unit__image-thumbnail']
                ),

                sprintf(
                    '<a class="unit__content-link" href="%1$s">%2$s</a>',
                    esc_url($attributes['link']),
                    esc_html($attributes['title'])
                )
            )
        );

        return $content . $unit;
    }
}


/**
 * Load current module environment
 */
Knife_Select_Links::load_module();
