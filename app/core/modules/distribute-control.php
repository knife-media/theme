<?php
/**
 * Distribute control
 *
 * Schedule posts publishing to social networks
 *
 * @package knife-theme
 * @since 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Distribute_Control {
    /**
     * Default post type with social distribute metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-distribute-nonce';


    /**
     * Unique meta to store distribute items
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-distribute-items';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom distribute metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);
    }


    /**
     * Add custom distribute metabox for editors and admins
     */
    public static function add_metabox() {
        if(current_user_can('publish_pages')) {
            add_meta_box('knife-distribute-metabox', __('Настройки кросспостинга'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');

            // Enqueue post metabox scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        }
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        // Current screen object
        $screen = get_current_screen();

        if(!in_array($screen->post_type,  self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-distribute-metabox', $include . '/styles/distribute-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-distribute-metabox', $include . '/scripts/distribute-metabox.js', ['jquery'], $version);

        $options = [
            'meta_items' => esc_attr(self::$meta_items),
            'choose' => __('Выберите изображение', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-distribute-metabox', 'knife_distribute_metabox', $options);
    }


    /**
     * Display distribute metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/distribute-metabox.php');
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id) {
        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('publish_pages', $post_id)) {
            return;
        }


        // Update items
        self::update_items(self::$meta_items, $post_id);
    }


    /**
     * Update distribute items from post-metabox
     */
    private static function update_items($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete distribute post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            // Sanitize item values
            foreach($item as $key => &$value) {
                switch($key) {
                    case 'network':
                        $value = array_map('sanitize_text_field', $value) ;
                        break;

                    case 'excerpt':
                        $value = sanitize_textarea_field($value);
                        break;

                    case 'attachment':
                        $value = absint($value);
                        break;

                    case 'delay':
                        $value = absint($value);
                }
            }

            // Add post meta if not empty
            if(array_filter($item)) {
                add_post_meta($post_id, $query, $item);
            }
        }
    }
}


/**
 * Load module
 */
Knife_Distribute_Control::load_module();
