<?php
/**
 * ID handler
 *
 * Prepare data for id handler module
 *
 * @package knife-theme
 * @since 1.13
 */


if (!defined('WPINC')) {
    die;
}

class Knife_ID_Handler {
    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_request = 'knife-id-request';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Include required id loader data
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);
    }


    /**
     * Include required id loader data
     */
    public static function inject_object() {
        if(!is_singular()) {
            return;
        }

        $options = [
            'post' => get_queried_object_id(),
            'reply' => __('Ответить', 'knife-theme'),
            'remove' => __('Удалить', 'knife-theme'),
            'block' => __('Забанить', 'knife-theme'),
            'submit' => __('Отправить', 'knife-theme'),
            'placeholder' => __('Что вы об этом думаете?', 'knife-theme'),
            'anonymous' => __('Анонимный пользователь', 'knife-theme'),
            'avatar' => get_template_directory_uri() . '/assets/images/no-avatar.png',
        ];

        if (current_user_can('publish_posts')) {
            $options = array_merge($options, [
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'action' => self::$ajax_request,
                'nonce' => wp_create_nonce(self::$ajax_request)
            ]);
        }

        wp_localize_script('knife-theme', 'knife_id_handler', $options);
    }
}


/**
 * Load current module environment
 */
Knife_ID_Handler::load_module();