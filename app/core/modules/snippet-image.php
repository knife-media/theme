<?php
/**
 * Snippet image
 *
 * Create sharing image for social networks
 *
 * @package knife-theme
 * @since 1.9
 * @version 1.16
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Snippet_Image {
    /**
     * Backward compatibility social image meta name
     *
     * @access  public
     * @var     string
     */
    public static $meta_image = '_social-image';


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
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'quiz', 'page'];


    /**
     * Unique meta to store custom term snippet
     *
     * @access  public
     * @var     string
     * @since   1.12
     */
    public static $term_meta = '_knife-term-snippet';


    /**
     * Default taxes term snippets availible
     *
     * @access  public
     * @var     array
     * @since   1.12
     */
    public static $taxonomies = ['special'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-snippet-nonce';


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
        // Add snippet image metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_meta'], 10, 2);

        // Enqueue metabox scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Generate poster using ajax options
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'generate_poster']);

        // Add snippet term fields
        add_action('admin_init', [__CLASS__, 'add_term_fields']);
    }


    /**
     * Public function to get social image outside the module
     *
     * @since 1.11
     */
    public static function get_social_image() {
        $object_id = get_queried_object_id();

        // Default social image
        $social_image = get_template_directory_uri() . '/assets/images/poster-feature.png';

        // Get snippet for singular posts
        if(is_singular() && !is_front_page()) {
            // Custom social image storing via social-image plugin in post meta
            $social_image = get_post_meta($object_id, self::$meta_image, true);

            if(empty($social_image) && has_post_thumbnail()) {
                return wp_get_attachment_image_src(get_post_thumbnail_id($object_id), 'outer');
            }

            $options = get_post_meta($object_id, self::$meta_options, true);

            // Set size using options
            $options = wp_parse_args($options, [
                'width' => 1200,
                'height' => 630
            ]);

            return array($social_image, $options['width'], $options['height']);
        }

        // Get snippet for taxonomy
        if(is_tax(self::$taxonomies)) {
            // Try to find snippet
            $snippet = get_term_meta($object_id, self::$term_meta, true);

            if($snippet) {
                return array($snippet, 1200, 630);
            }
        }

        return array($social_image, 1200, 630);
    }


    /**
     * Add tax terms snippet field
     *
     * @since 1.12
     */
    public static function add_term_fields() {
        foreach(self::$taxonomies as $tax) {
            add_action("{$tax}_edit_form_fields", [__CLASS__, 'print_term_row'], 8, 2);
            add_action("edited_{$tax}", [__CLASS__, 'save_term']);
        }
    }


    /**
     * Display term snippet row
     *
     * @since 1.12
     */
    public static function print_term_row($term, $taxonomy) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/snippet-options.php');
    }


    /**
     * Add snippet image metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-snippet-metabox', __('Изображение соцсети', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'side');
    }


    /**
     * Save term options
     *
     * @since 1.12
     */
    public static function save_term($term_id) {
        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        if(empty($_REQUEST[self::$term_meta])) {
            return delete_term_meta($term_id, self::$term_meta);
        }

        // Sanitize request field
        $snippet = sanitize_text_field($_REQUEST[self::$term_meta]);

        return update_term_meta($term_id, self::$term_meta, $snippet);
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

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
            'post_id' => absint($post->ID),
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$ajax_nonce),

            'choose' => __('Выберите изображение', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-snippet-metabox', 'knife_snippet_metabox', $options);
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
    public static function save_meta($post_id, $post) {
        if(isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
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
        check_admin_referer(self::$ajax_nonce, 'nonce');

        if(!method_exists('Knife_Poster_Templates', 'create_poster')) {
            wp_send_json_error(__('Модуль генерации не найден', 'knife-theme'));
        }

        $options = wp_parse_args($_REQUEST, [
            'template' => 'snippet',
            'post_id' => 0,
            'attachment' => 0
        ]);

        if(!current_user_can('edit_post', $options['post_id'])) {
            return;
        }

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
