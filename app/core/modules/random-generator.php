<?php
/**
* Generator type
*
* Custom post type for random generated answers
*
* @package knife-theme
* @since 1.6
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Random_Generator {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $slug = 'generator';


    /**
     * Generator share query var
     *
     * @access  private
     * @var     string
     */
    private static $query_var = 'share';


    /**
     * Unique nonce string
     *
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-generator-nonce';


   /**
    * Ajax action
    *
    * @access  private static
    * @var     string
    */
    private static $action = 'knife-generator-create';


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
        // Register select post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add rewrite url for sharing
        add_action('init', [__CLASS__, 'add_share_rule']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_share_var']);

        // Redirect to custom generated template if share query var exists
        add_action('template_redirect', [__CLASS__, 'redirect_share']);

        // Change single post type template path
        add_action('single_template', [__CLASS__, 'include_single']);

        // Add select metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Create poster
        add_action('wp_ajax_' . self::$action, [__CLASS__, 'create_poster']);

        // Include generator options
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_generator'], 12);
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                    => [
                'name'                  => __('Генератор', 'knife-theme'),
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
            'supports'              => ['title', 'thumbnail', 'excerpt', 'comments'],
            'taxonomies'            => ['post_tag'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 12,
            'menu_icon'             => 'dashicons-feedback',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
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
                self::$slug
            ),

            sprintf(
                'index.php?post_type=%1$s&%1$s=$matches[1]&%2$s=$matches[2]',
                self::$slug,
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
        if(is_singular(self::$slug)) {
            $post_id = get_the_ID();
            $options = get_post_meta($post_id, self::$meta_options, true);

            if(class_exists('Knife_Share_Buttons')) {
                $options = array_merge($options, [
                    'permalink' => get_permalink($post_id),
                    'share_links' => Knife_Share_Buttons::get_settings($post_id)
                ]);
            }

            // Add generator options object
            wp_localize_script('knife-theme', 'knife_generator_options', $options);

            // Add generator items
            wp_localize_script('knife-theme', 'knife_generator_items', self::retrieve_items($post_id));
        }
    }


    /**
     * Redirect to custom generated template if share query var exists
     */
    public static function redirect_share() {
        if(is_singular(self::$slug) && get_query_var('share')) {
            $post_id = get_the_ID();

            $share = absint(get_query_var('share')) - 1;
            $items = self::retrieve_items($post_id, true);

            if($share >= 0 && count($items) > $share) {
                extract($items[$share]);

                $include = get_template_directory() . '/core/include';
                include_once($include . '/templates/generator-share.php');

                exit;
            }
        }
    }


    /**
     * Include single select template
     */
    public static function include_single($template) {
        if(is_singular(self::$slug)) {
            $new_template = locate_template(['templates/single-generator.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Create poster using ajax options
     */
    public static function create_poster() {
        check_admin_referer(self::$nonce, 'nonce');

        if(!class_exists('PHPImage')) {
            require(get_template_directory() . '/core/classes/phpimage.class.php');
        }

        $default = [
            'caption' => '', 'post_id' => 0, 'attachment' => 0
        ];

        $options = wp_parse_args(
            array_intersect_key($_REQUEST, $default), $default
        );

        extract($options);

        $attachment = get_attached_file($attachment);

        if($attachment === false) {
            wp_send_json_error(__('Не удалось найти вложение', 'knife-theme'));
        }

        $poster = self::generate_image($attachment, $caption, $post_id);

        return wp_send_json_success($poster);
    }


    /**
     * Add select metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-generator-metabox', __('Настройки генератора'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
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

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin styles
        wp_enqueue_style('knife-random-generator', $include . '/styles/random-generator.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-random-generator', $include . '/scripts/random-generator.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'post_id' => absint($post_id),
            'action' => esc_attr(self::$action),
            'nonce' => wp_create_nonce(self::$nonce),
            'choose' => __('Выберите изображение постера', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-random-generator', 'knife_random_generator', $options);
    }


    /**
     * Display select link metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/generator-metabox.php');
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

        update_post_meta($post_id, self::$meta_options, $_REQUEST[self::$meta_options]);


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
     * Retrieve items within meta to show as object
     */
    private static function retrieve_items($post_id, $raw = false, $items = []) {
        $options = ['description', 'caption', 'poster'];

        foreach(get_post_meta($post_id, self::$meta_items) as $meta) {
            if(array_diff_key(array_flip($options), $meta)) {
                continue;
            }

            if($raw === false) {
                $items[] = [
                    'description' => apply_filters('the_content', $meta['description']),
                    'caption' => esc_html($meta['caption']),
                    'poster' => esc_url($meta['poster'])
                ];

                continue;
            }

            $items[] = $meta;
        }

        return $items;
    }


    /**
     * Generate image using PHPImage class
     */
    private static function generate_image($image, $caption, $post_id) {
        $upload = wp_upload_dir();

        if(!wp_is_writable($upload['basedir'] . self::$upload_folder)) {
            if(!mkdir($upload['basedir'] . self::$upload_folder)) {
                wp_send_json_error(__('Проверьте права на запись', 'knife-theme'));
            }
        }

        $file_name = self::$upload_folder . "{$post_id}-" . time() . '.jpg';

        try {
            $poster = new PHPImage();
            $poster->setDimensionsFromImage($image)->draw($image);
            $poster->resize(1200, 630, true);

            $poster->setFont(get_template_directory() . '/assets/fonts/formular/formular-black.ttf');
            $poster->setTextColor([255, 255, 255]);
            $poster->textBox(mb_strtoupper($caption), [
                'x' => 48,
                'y' => 290,
                'width' => 1000,
                'fontSize' => 64
            ]);

            $poster->snapshot($upload['basedir'] . $file_name);

        } catch(Exception $error) {
            wp_send_json_error(__('Ошибка генерации: ' . $error->getMessage(), 'knife-theme'));
        }

        return $upload['baseurl'] . $file_name;
    }
}


/**
 * Load current module environment
 */
Knife_Random_Generator::load_module();
