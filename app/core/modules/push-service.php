<?php
/**
* Push notifications
*
* Use OneSignal as push sender service
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Push_Service {
   /**
    * Post meta name
    *
    * @access  private
    * @var     string
    */
    private static $meta  = '_knife-push';


   /**
    * Option to store push settings
    *
    * @access  private
    * @var     string
    */
    private static $option = 'knife-push-settings';


   /**
    * Default post type push service availible
    *
    * @access  private
    * @var     array
    */
    private static $type = ['post'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Update type array by filters
        add_action('init', [__CLASS__, 'set_type'], 20);

        // Add admin side assets
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Include OneSignal js sdk
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Plugin settings
        add_action('admin_init', [__CLASS__, 'settings_init']);
        add_action('admin_menu', [__CLASS__, 'add_menu']);

        // Post metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Ajax handler
        add_action('wp_ajax_knife_push_send', [__CLASS__, 'send_push']);
    }


    /**
     * Update type array by modules filters
     */
    public static function set_type() {
       /**
         * Filter push service support post types
         *
         * @since 1.3
         * @param array $type
         */
        self::$type = apply_filters('knife_push_service_type', self::$type);
    }


   /**
    * Enqueue assets to admin post screen only
    */
    public static function enqueue_assets($hook) {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_script('knife-push-service', $include . '/scripts/push-service.js', [], $version);
    }


    /**
     * Include app id for OneSignal API to knife-theme js script
     */
    public static function inject_object() {
        $options = get_option(self::$option);

        if(empty($options['appid']) || empty($options['popup'])) {
            return false;
        }

        $include = [
            'appid' => $options['appid'],
            'button' => __('Подписаться', 'knife-theme'),
            'promo' => __('Получать последние обновления&nbsp;сайта', 'knife-theme')
        ];

        wp_localize_script('knife-theme', 'knife_push', $include);
    }


    /**
     * Add push settings submenu to main options menu
     */
    public static function add_menu() {
        add_submenu_page('options-general.php',
            __('Настройки пушей', 'knife-theme'),
            __('Push-уведомления', 'knife-theme'),
            'manage_options',
            'knife-push',
            [__CLASS__, 'settings_page']
        );
    }


    /**
     * Display push options page
     */
     public static function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-push-settings');
        do_settings_sections('knife-push-settings');
        submit_button();

        echo '</form>';
    }


    /**
     * Add push sending metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-push-metabox',
            __('Отправить пуш', 'knife-theme'),
            [__CLASS__, 'display_metabox'],
            self::$type, 'side', 'low'
        );
    }


    /**
     * Display push sending metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/push-metabox.php');
    }


    /**
     * Register settings forms
     */
    public static function settings_init() {
        register_setting('knife-push-settings', self::$option);

        add_settings_section(
            'knife-push-section',
            __('Настройки пушей', 'knife-theme'),
            [],
            'knife-push-settings'
        );

        add_settings_field(
            'appid',
            __('OneSignal App ID', 'knife-theme'),
            [__CLASS__, 'setting_render_appid'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'rest',
            __('REST API Key', 'knife-theme'),
             [__CLASS__, 'setting_render_rest'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'segments',
            __('Сегменты рассылки (через запятую)', 'knife-theme'),
             [__CLASS__, 'setting_render_segments'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'title',
            __('Заголовок пуша по умолчанию', 'knife-theme'),
             [__CLASS__, 'setting_render_title'],
            'knife-push-settings',
             'knife-push-section'
        );

         add_settings_field(
            'utm',
            __('Параметры ссылки', 'knife-theme'),
             [__CLASS__, 'setting_render_utm'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'popup',
            __('Показывать popup', 'knife-theme'),
             [__CLASS__, 'setting_render_popup'],
            'knife-push-settings',
             'knife-push-section'
        );

    }

    public static function setting_render_appid() {
        $options = get_option(self::$option);
        $default = isset($options['appid']) ? $options['appid'] : '';

        printf(
            '<input type="text" name="%1$s[appid]" class="widefat" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_rest() {
        $options = get_option(self::$option);
        $default = isset($options['rest']) ? $options['rest'] : '';

        printf(
            '<input type="text" name="%1$s[rest]" class="widefat" value="%2$s">',
             self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_segments() {
        $options = get_option(self::$option);
        $default = isset($options['segments']) ? $options['segments'] : '';

        printf(
            '<input type="text" name="%1$s[segments]" placeholder="All" class="widefat" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_title() {
        $options = get_option(self::$option);
        $default = isset($options['title']) ? $options['title'] : '';

        printf(
            '<input type="text" name="%1$s[title]" class="widefat" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

     public static function setting_render_utm() {
        $options = get_option(self::$option);
        $default = isset($options['utm']) ? $options['utm'] : '';

        printf(
            '<input type="text" name="%1$s[utm]" placeholder="utm_source=site&utm_medium=webpush" class="widefat" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_popup() {
        $options = get_option(self::$option);
        $default = isset($options['popup']) ? $options['popup'] : 0;

        printf(
            '<input type="checkbox" name="%1$s[popup]" value="1" %2$s>',
            self::$option,
            checked($default, 1, false)
        );
    }



    /**
     * Send push using OneSignal API
     */
    public static function send_push() {
        $post_id = $_POST['post'];

        if(empty($post_id))
            wp_send_json_error(__("Неверный ID записи", 'knife-theme'));

        $opts = get_option(self::$option);

        if(empty($opts['appid']) || empty($opts['rest']))
            wp_send_json_error(__("Необходимо заполнить опции на странице настроек", 'knife-theme'));

        if(empty($opts['segments']))
            $opts['segments'] = 'All';

        parse_str($opts['utm'], $args);

        $fields = array(
            'app_id' => $opts['appid'],

            'included_segments' => explode(",", $opts['segments']),

            'contents' => [
                'en' => $_POST['message']
            ],

            'headings' => [
                'en' => $_POST['title']
            ],

            'url' => add_query_arg($args, get_permalink($post_id))
        );

        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $opts['rest']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        $answer = json_decode($response);

        if(!isset($answer->id))
            wp_send_json_error(__("Пуш не отправлен. Что-то пошло не так", 'knife-theme'));

        update_post_meta($post_id, self::$meta, $answer->id);

        wp_send_json_success(__("Пуш успешно отправлен", 'knife-theme'));
    }
}


/**
 * Load module
 */
Knife_Push_Service::load_module();

