<?php
/**
 * ID handler
 *
 * Prepare data for id handler module
 *
 * @package knife-theme
 * @since 1.13
 * @version 1.14
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

        // Define id settings if still not
        if(!defined('KNIFE_ID')) {
            define('KNIFE_ID', []);
        }
    }


    /**
     * Remove discussion options page
     */
    public static function remove_settings() {
        remove_submenu_page('options-general.php', 'options-discussion.php');
    }


    /**
     * Try to remove comment if needed
     */
    private static function remove_comment($db, $fields) {
        if (empty($fields['remove'])) {
            return;
        }

        $comment = absint($fields['remove']);

        // Try to remove comment
        $remove = $db->update('comments', ['status' => 'removed'], ['id' => $comment]);

        if(!$remove) {
            wp_send_json_error(__('Не удалось удалить комментарий', 'knife-theme'));
        }

        wp_send_json_success();
    }


    /**
     * Try to block user and remove his comments
     */
    private static function block_user($db, $fields) {
        if (empty($fields['block'])) {
            return;
        }

        $comment = absint($fields['block']);

        // Get user id
        $user_id = $db->get_var(
            $db->prepare("SELECT user_id FROM comments WHERE id = %d", $comment)
        );

        if($user_id === null) {
            wp_send_json_error(__('Не удалось найти пользователя', 'knife-theme'));
        }

        // Set blocked status to user
        $blocked = $db->update('users', ['status' => 'blocked'], ['id' => $user_id]);

        if($blocked === false) {
            wp_send_json_error(__('Не удалось заблокировать пользователя', 'knife-theme'));
        }

        // Remove all user comments
        $remove = $db->update('comments', ['status' => 'removed'], ['user_id' => $user_id]);

        if($remove === false) {
            wp_send_json_error(__('Не удалось удалить комментарии пользователя', 'knife-theme'));
        }

        wp_send_json_success();
    }


    /**
     * Handle admin requests
     */
    public static function handle_request() {
        if(!check_ajax_referer(self::$ajax_request, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }

        if(!current_user_can('publish_posts')) {
            wp_send_json_error(__('Для вашего пользователя это действие недоступно', 'knife-theme'));
        }

        // Mix with default values
        $conf = wp_parse_args(KNIFE_ID, [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD
        ]);

        // Create custom db connection
        $db = new wpdb($conf['user'], $conf['password'], $conf['name'], $conf['host']);
        $db->hide_errors();

        if(isset($db->error)) {
            wp_send_json_error(__('Не удалось соединиться с удаленной базой данных', 'knife-theme'));
        }

        // Try to remove comment
        self::remove_comment($db, wp_unslash($_REQUEST));

        // Try to block user
        self::block_user($db, wp_unslash($_REQUEST));
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
                'blocked' => __('Пользователь заблокирован, и все его сообщения будут удалены', 'knife-theme'),
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