<?php
/**
 * Snippet image
 *
 * Create sharing image for social networks
 *
 * @package knife-theme
 * @since 1.9
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Snippet_Image {
   /**
    * Backward compatibility social image meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta_image = '_social-image';


   /**
    * Backward compatibility options meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta_options = '_social-image-options';


    /**
     * Default post type with snippet image metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz', 'select', 'story', 'generator'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-snippet-nonce';


    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_action = 'knife-snippet-poster';


    /**
     * Directory to save social snippets images
     *
     * @access  private
     * @var     string
     */
    private static $upload_folder = '/social-image/';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        if(current_user_can('edit_posts')) {
            // Add custom distribute metabox
            add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

            // Save metabox
            add_action('save_post', [__CLASS__, 'save_metabox'], 10, 2);

            // Enqueue dashboard widget scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

            // Generate poster using ajax options
            add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'generate_poster']);
        }
    }


    /**
     * Add snippet image metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-snippet-metabox', __('Изображение соцсети', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'side');
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Metabox styles
        if(in_array($hook, ['post.php', 'post-new.php'])) {
            $post_id = get_the_ID();

            // Current screen object
            $screen = get_current_screen();

            if(!in_array($screen->post_type, self::$post_type)) {
                return;
            }

            // Insert wp media scripts
            wp_enqueue_media();

            // Insert admin styles
            wp_enqueue_style('knife-snippet-metabox', $include . '/styles/snippet-metabox.css', [], $version);

            // Insert admin scripts
            wp_enqueue_script('knife-snippet-metabox', $include . '/scripts/snippet-metabox.js', ['jquery'], $version);

            $options = [
                'post_id' => absint($post_id),
                'action' => esc_attr(self::$ajax_action),
                'nonce' => wp_create_nonce(self::$metabox_nonce),

                'choose' => __('Выберите изображение', 'knife-theme'),
                'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
            ];

            wp_localize_script('knife-snippet-metabox', 'knife_snippet_metabox', $options);
        }
    }


    /**
     * Display snippet-image metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/snippet-metabox.php');
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id, $post) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Add updated image sizes
        $options = [];

        if(isset($_REQUEST[self::$meta_options])) {
            $options = $_REQUEST[self::$meta_options];
        }

        $options = wp_parse_args($options, [
            'width' => 1200,
            'height' => 630
        ]);

        // Update options
        update_post_meta($post_id, self::$meta_options, $options);

        // Save social-image meta
        if(empty($_REQUEST[self::$meta_image])) {
            return delete_post_meta($post_id, self::$meta_image);
        }

        update_post_meta($post_id, self::$meta_image, $_REQUEST[self::$meta_image]);
    }


    /**
     * Generate poster using ajax options
     */
    public static function generate_poster() {
        check_admin_referer(self::$metabox_nonce, 'nonce');

        if(!method_exists('Knife_Poster_Templates', 'create_poster')) {
            wp_send_json_error(__('Модуль генерации не найден', 'knife-theme'));
        }

        $options = wp_parse_args($_REQUEST, [
            'template' => 'snippet',
            'post_id' => 0,
            'attachment' => 0
        ]);

        $poster = Knife_Poster_Templates::create_poster($options, self::$upload_folder);

        if(is_wp_error($poster)) {
            wp_send_json_error($poster->get_error_message());
        }

        wp_send_json_success($poster);
    }
}


/**
 * Load module
 */
Knife_Snippet_Image::load_module();
