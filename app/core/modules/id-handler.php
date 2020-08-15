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

        // Prevent to create WordPress posts and pages with custom reserved slug
        add_filter('wp_unique_post_slug_is_bad_flat_slug', [__CLASS__, 'prevent_slug'], 10, 2);
        add_filter('wp_unique_post_slug_is_bad_hierarchical_slug', [__CLASS__, 'prevent_slug'], 10, 2);

        // Remove discussion options page
        add_action('admin_menu', [__CLASS__, 'remove_settings']);

        // Handle admin requests
        add_action('wp_ajax_' . self::$ajax_request, [__CLASS__, 'handle_request']);
    }


    /**
     * Remove discussion options page
     */
    public static function remove_settings() {
        remove_submenu_page('options-general.php', 'options-discussion.php');
    }


    /**
     * Handle admin requests
     */
    public static function handle_request() {
        if(!check_ajax_referer(self::$ajax_request, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }
    }


    /**
     * Prevent to create WordPress posts and pages with custom reserved slug
     */
    public static function prevent_slug($bad_slug, $slug) {
        if($slug === 'id') {
            return true;
        }

        return $bad_slug;
    }


    /**
     * Generate localize policy link
     */
    public static function get_policy_link() {
        $policy = sprintf(
            __('Авторизуясь, вы соглашаетесь с условиями  <a href="%s" target="_blank">пользовательского соглашения</a> и даете согласие на обработку персональных данных.', 'knife-theme'),
            esc_url(get_permalink(get_page_by_path('/user-agreement/')))
        );

        return $policy;
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

            'form' => [
                'submit' => __('Написать', 'knife-theme'),
                'authorize' => __('Войти и написать', 'knife-theme'),
                'placeholder' => __('Что вы об этом думаете?', 'knife-theme'),
                'reply' => __('Напишите свой ответ…', 'knife-theme'),
                'cancel' => __('Отменить', 'knife-theme'),
                'exit' => __('Выйти из профиля', 'knife-theme')
            ],

            'comments' => [
                'anonymous' => __('Анонимный пользователь', 'knife-theme'),
                'removed' => __('Сообщение было удалено', 'knife-theme'),
                'reply' => __('Ответить', 'knife-theme'),
                'remove' => __('Удалить', 'knife-theme'),
                'block' => __('Забанить', 'knife-theme'),
                'expand' => __('Показать все комментарии', 'knife-theme'),
                'noavatar' => get_template_directory_uri() . '/assets/images/no-avatar.png'
            ],

            'login' => [
                'policy' => self::get_policy_link(),
                'heading' => __('Войти через соцсеть', 'knife-theme'),
                'helper' => __('Для того, чтобы оставлять комментарии и ставить оценки.', 'knife-theme'),
                'vkontakte' => __('ВКонтакте', 'knife-theme'),
                'google' => __('Google', 'knife-theme'),
                'yandex' => __('Яндекс', 'knife-theme'),
                'facebook' => __('Facebook', 'knife-theme')
            ],

            'error' => __('Произошла непредвиденная ошибка. Повторите попытку позже.', 'knife-theme')
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