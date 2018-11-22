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
     * Unique meta to store generator catalog
     *
     * @access  private
     * @var     string
     */
    private static $meta_catalog = '_knife-generator-catalog';


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
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                => [
                'name'              => __('Генератор', 'knife-theme'),
                'singular_name'     => __('Генератор', 'knife-theme'),
                'add_new'           => __('Добавить генератор', 'knife-theme'),
                'menu_name'         => __('Генераторы', 'knife-theme')
            ],
            'label'                 => __('Генератор', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'excerpt'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 12,
            'menu_icon'             => 'dashicons-feedback',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true
        ]);
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
            'action' => esc_attr(self::$action),
            'nonce' => wp_create_nonce(self::$nonce),
            'post' => absint($post_id),
            'choose' => __('Выберите изображение постера', 'knife-theme')
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
        self::update_items(self::$meta_catalog, $post_id);
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
                'y' => 275,
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
