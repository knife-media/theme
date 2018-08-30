<?php
/**
* Post sticker meta
*
* Custom optional image for posts
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Post_Sticker {
   /**
    * Post meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta = '_knife-sticker';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // post sticker
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        add_action('wp_ajax_knife_sticker_upload', [__CLASS__, 'upload_sticker']);
        add_action('wp_ajax_knife_sticker_delete', [__CLASS__, 'delete_sticker']);
    }


     /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'post') {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style('knife-post-sticker', $include . '/styles/post-sticker.css', [], $version);

        // insert admin scripts
        wp_enqueue_script('knife-post-sticker', $include . '/scripts/post-sticker.js', ['jquery'], $version);

        $options = [
            'choose' => __('Выберите изображение для стикера', 'knife-theme')
        ];

        wp_localize_script('knife-post-sticker', 'knife_post_sticker', $options);
    }


    /**
     * Add meta box to admin post screen
     */
    public static function add_metabox() {
        add_meta_box('knife-sticker-metabox', __('Стикер', 'knife-theme'), [__CLASS__, 'print_metabox'], 'post', 'side');
    }


    /**
     * Display meta box template
     */
    public static function print_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/sticker-metabox.php');
    }


    /**
     * Ajax based image uploader
     */
    public static function upload_sticker() {
        $post_id = intval($_POST['post']);
        $sticker = intval($_POST['sticker']);

        if(!current_user_can('edit_post', $post_id)) {
            wp_send_json_error(__('Нет прав на редактирование записи', 'knife-theme'));
        }

        $file = self::set_filename($post_id, '/stickers/');

        $image = wp_get_image_editor(get_attached_file($sticker));

        if(is_wp_error($image)) {
            wp_send_json_error($image->get_error_message());
        }

        $image->resize(150, 150, true);
        $image->save($file['dir'], 'image/png');

        if(is_wp_error($image))
            wp_send_json_error($image->get_error_message());

        $meta = update_post_meta($post_id, self::$meta, $file['url']);

        if($meta) {
            return wp_send_json_success($file['url']);
        }

        return wp_send_json_error(__('Ошибка при сохранении стикера', 'knife-theme'));
    }


    /**
     * Remove meta from post
     */
    public static function delete_sticker() {
        $post_id = intval($_POST['post']);

        if(!current_user_can('edit_post', $post_id)) {
            wp_send_json_error(__('Нет прав на редактирование записи', 'knife-theme'));
        }

        if(delete_post_meta($post_id, self::$meta)) {
            wp_send_json_success(__('Стикер yспешно удален', 'knife-theme'));
        }

        wp_send_json_error(__('Не удалось удалить стикер', 'knife-theme'));
    }


    /**
     * Generate new sticker filename
     */
    private static function set_filename($post_id, $folder) {
        $upload = wp_upload_dir();
        $create = $upload['basedir'] . $folder;

        if(!is_dir($create) && !mkdir($create)) {
            wp_send_json_error(__('Проверьте права на запись папки загрузок', 'knife-theme'));
        }

        $file = $folder . "{$post_id}-" . time() . '.png';

        return ['dir' => $upload['basedir'] . $file, 'url' => $upload['baseurl'] . $file];
    }
}


/**
 * Load current module environment
 */
Knife_Post_Sticker::load_module();
