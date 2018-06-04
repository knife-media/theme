<?php
/**
* Post sticker meta
*
* Custom optional image for posts
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Post_Sticker;

class Knife_Post_Sticker {
    private $meta = '_knife-sticker';

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // post sticker
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        add_action('wp_ajax_knife_sticker_upload', [$this, 'upload_sticker']);
        add_action('wp_ajax_knife_sticker_delete', [$this, 'delete_sticker']);
    }


     /**
     * Enqueue assets to admin post screen only
     */
    public function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php']))
            return;

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'post')
            return;

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
    public function add_metabox() {
        add_meta_box('knife-sticker-metabox', __('Стикер', 'knife-theme'), [$this, 'print_metabox'], 'post', 'side');
    }


    /**
     * Display meta box template
     */
    public function print_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/sticker-metabox.php');
    }


    /**
     * Ajax based image uploader
     */
    public function upload_sticker() {
        $post_id = intval($_POST['post']);
        $sticker = intval($_POST['sticker']);

        if(!current_user_can('edit_post', $post_id))
            wp_send_json_error(__('Нет прав на редактирование записи', 'knife-theme'));

        $file = $this->set_filename($post_id, '/stickers/');

        $image = wp_get_image_editor(get_attached_file($sticker));

        if(is_wp_error($image))
            wp_send_json_error($image->get_error_message());

        $image->resize(150, 150, true);
        $image->save($file['dir'], 'image/png');

        if(is_wp_error($image))
            wp_send_json_error($image->get_error_message());

        $meta = update_post_meta($post_id, $this->meta, $file['url']);

        if($meta)
            return wp_send_json_success($file['url']);

        return wp_send_json_error(__('Ошибка при сохранении стикера', 'knife-theme'));
    }


    /**
     * Remove meta from post
     */
    public function delete_sticker() {
        $post_id = intval($_POST['post']);

        if(!current_user_can('edit_post', $post_id))
            wp_send_json_error(__('Нет прав на редактирование записи', 'knife-theme'));

        if(delete_post_meta($post_id, $this->meta))
            wp_send_json_success(__('Стикер yспешно удален', 'knife-theme'));

        wp_send_json_error(__('Не удалось удалить стикер', 'knife-theme'));
    }


    /**
     * Generate new sticker filename
     */
    private function set_filename($post_id, $folder) {
        $upload = wp_upload_dir();
        $create = $upload['basedir'] . $folder;

        if(!is_dir($create) && !mkdir($create))
            wp_send_json_error(__('Проверьте права на запись папки загрузок', 'knife-theme'));

        $file = $folder . "{$post_id}-" . time() . '.png';

        return ['dir' => $upload['basedir'] . $file, 'url' => $upload['baseurl'] . $file];
    }
}
