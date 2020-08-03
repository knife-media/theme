<?php
/**
 * Generator type
 *
 * Custom post type for random generated answers
 *
 * @package knife-theme
 * @since 1.6
 * @version 1.13
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Generator_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  public
     * @var     string
     */
    public static $post_type = 'generator';


    /**
     * Generator share query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'share';


    /**
     * Unique nonce string
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-generator-nonce';


   /**
    * Poster ajax action
    *
    * @access  private
    * @var     string
    */
    private static $ajax_action = 'knife-generator-poster';


    /**
     * Unique meta to store generator options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-generator-options';


    /**
     * Unique meta to store generator items
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-generator-items';


    /**
     * Directory to save generator images
     *
     * @access  private
     * @var     string
     */
    private static $upload_folder = '/generator/';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register generator post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add rewrite url for sharing
        add_action('init', [__CLASS__, 'add_share_rule']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_share_var']);

        // Redirect to custom generated template if share query var exists
        add_action('template_redirect', [__CLASS__, 'redirect_share']);

        // Change single post type template path
        add_action('single_template', [__CLASS__, 'include_single']);

        // Add generator metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Create poster
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'generate_poster']);

        // Include generator options
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_generator'], 12);

        // Add quiz post type to archives
        add_action('pre_get_posts', [__CLASS__, 'update_archives'], 12);
    }


    /**
     * Register generator post type
     */
    public static function register_type() {
        register_post_type(self::$post_type, [
            'labels'                    => [
                'name'                  => __('Генераторы', 'knife-theme'),
                'singular_name'         => __('Генератор', 'knife-theme'),
                'add_new'               => __('Добавить генератор', 'knife-theme'),
                'menu_name'             => __('Генераторы', 'knife-theme'),
                'all_items'             => __('Все генераторы', 'knife-theme'),
                'add_new_item'          => __('Добавить новый генератор', 'knife-theme'),
                'new_item'              => __('Новый генератор', 'knife-theme'),
                'edit_item'             => __('Редактировать генератор', 'knife-theme'),
                'update_item'           => __('Обновить генератор', 'knife-theme'),
                'view_item'             => __('Просмотреть генератор', 'knife-theme'),
                'view_items'            => __('Просмотреть подборки', 'knife-theme'),
                'search_items'          => __('Искать генератор', 'knife-theme'),
                'insert_into_item'      => __('Добавить в генератор', 'knife-theme')
            ],
            'label'                 => __('Генератор', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'excerpt', 'comments', 'author'],
            'taxonomies'            => ['post_tag', 'category'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 12,
            'menu_icon'             => 'dashicons-feedback',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Add rewrite sharing parameter to post type url
     *
     * Mask: /%post-type%/%post-name%/%item-number%/
     */
    public static function add_share_rule() {
        add_rewrite_rule(
            sprintf('%s/([^/]+)/([^/]+)/?$',
                self::$post_type
            ),

            sprintf(
                'index.php?post_type=%1$s&%1$s=$matches[1]&%2$s=$matches[2]',
                self::$post_type,
                self::$query_var
            ),

            'top'
        );
    }


    /**
     * Append share query tag to availible query vars
     */
    public static function append_share_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include generator meta options and items
     */
    public static function inject_generator() {
        $post_id = get_queried_object_id();

        if(is_singular(self::$post_type)) {
            $options = get_post_meta($post_id, self::$meta_options, true);

            if(!is_array($options)) {
                $options = [];
            }

            if(class_exists('Knife_Share_Buttons')) {
                $options = array_merge($options, [
                    'permalink' => get_permalink($post_id),
                    'share_links' => Knife_Share_Buttons::get_settings($post_id)
                ]);
            }

            // Add generator options object
            wp_localize_script('knife-theme', 'knife_generator_options', $options);

            // Add generator items
            wp_localize_script('knife-theme', 'knife_generator_items',
                (array) self::retrieve_items($post_id, $options)
            );
        }
    }


    /**
     * Redirect to custom generated template if share query var exists
     */
    public static function redirect_share() {
        $share = get_query_var(self::$query_var);

        if(is_singular(self::$post_type) && strlen($share) > 0) {
            $post_id = get_queried_object_id();

            // Get generator options
            $options = get_post_meta($post_id, self::$meta_options, true);

            // Get generator items
            $items = self::retrieve_items($post_id, $options, true);
            $share = absint($share) - 1;

            if(isset($items[$share])) {
                $blanks = array_fill_keys(['heading', 'description', 'poster'], '');

                $item = wp_parse_args(
                    array_intersect_key($items[$share], $blanks), $blanks
                );

                extract($item);

                $include = get_template_directory() . '/core/include';
                include_once($include . '/templates/generator-share.php');

                exit;
            }
        }
    }


    /**
     * Include single generator template
     */
    public static function include_single($template) {
        if(is_singular(self::$post_type)) {
            $new_template = locate_template(['templates/single-generator.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Append generator posts to archives
     */
    public static function update_archives($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        // Is in archive
        foreach(['tag', 'category', 'author', 'date', 'home', 'tax'] as $archive) {
            $method = 'is_' . $archive;

            if($query->$method()) {
                $types = $query->get('post_type');

                if(!is_array($types)) {
                    $types = ['post'];
                }

                $types[] = self::$post_type;
                $query->set('post_type', $types);

                return false;
            }
        }
    }


    /**
     * Generate poster using ajax options
     */
    public static function generate_poster() {
        check_admin_referer(self::$ajax_nonce, 'nonce');

        if(!method_exists('Knife_Poster_Templates', 'create_poster')) {
            wp_send_json_error(__('Модуль генерации не найден', 'knife-theme'));
        }

        $options = wp_parse_args($_REQUEST, [
            'template' => '',
            'post_id' => 0,
            'attachment' => 0
        ]);

        $poster = Knife_Poster_Templates::create_poster($options, self::$upload_folder);

        if(is_wp_error($poster)) {
            wp_send_json_error($poster->get_error_message());
        }

        wp_send_json_success($poster);
    }


    /**
     * Add generator metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-generator-metabox', __('Настройки генератора', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'normal', 'high');
    }


    /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        if(get_post_type($post->ID) !== self::$post_type) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin styles
        wp_enqueue_style('knife-generator-metabox', $include . '/styles/generator-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-generator-metabox', $include . '/scripts/generator-metabox.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'post_id' => absint($post->ID),
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$ajax_nonce),
            'meta_items' => esc_attr(self::$meta_items),

            'choose' => __('Выберите изображение постера', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-generator-metabox', 'knife_generator_metabox', $options);
    }


    /**
     * Display generator link metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/generator-metabox.php');
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$post_type) {
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
            update_post_meta($post_id, self::$meta_options, $_REQUEST[self::$meta_options]);
        }

        // Update items meta
        self::update_items(self::$meta_items, $post_id);
    }


    /**
     * Update generator items meta from post-metabox
     */
    private static function update_items($query, $post_id) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete generator post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach((array) $_REQUEST[$query] as $request) {
            $item = [];

            if(isset($request['heading'])) {
                $item['heading'] = sanitize_text_field($request['heading']);
            }

            if(isset($request['template'])) {
                $item['template'] = sanitize_text_field($request['template']);
            }

            if(isset($request['poster'])) {
                $item['poster'] = sanitize_text_field($request['poster']);
            }

            if(isset($request['description'])) {
                $item['description'] = wp_kses_post($request['description']);

                // Add target to all links
                $item['description'] = wp_targeted_link_rel(links_add_target($item['description']));
            }

            if(isset($request['attachment'])) {
                $item['attachment'] = absint($request['attachment']);
            }

            // Add post meta if not empty
            if(array_filter($item)) {
                add_post_meta($post_id, $query, $item);
            }
        }
    }


    /**
     * Retrieve items within meta to show as object
     */
    private static function retrieve_items($post_id, $options, $raw = false) {
        $items = [];

        // Loop through items
        foreach(get_post_meta($post_id, self::$meta_items) as $meta) {
            $item = [];

            if(!empty($meta['heading'])) {
                $item['heading'] = esc_html($meta['heading']);
            }

            if(!empty($meta['description'])) {
                $item['description'] = wp_specialchars_decode(strip_tags($meta['description']));

                if($raw === false) {
                    $item['description'] = apply_filters('the_content', $meta['description']);
                }
            }

            if(empty($options['blank']) && !empty($meta['poster'])) {
                $item['poster'] = esc_url($meta['poster']);
            }

            $items[] = $item;
        }

        return $items;
    }
}


/**
 * Load current module environment
 */
Knife_Generator_Section::load_module();
