<?php
/**
* Push notifications
*
* Use OneSignal as push sender service
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Push_Service;

class Knife_Push_Service {
    private $meta   = '_knife-push';

    private $option = 'knife-push-settings';

    private $type = ['post'];

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // include OneSignal js sdk
        add_action('wp_enqueue_scripts', [$this, 'inject_object'], 12);

        // plugin settings
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'add_menu']);

        // post metabox
        add_action('add_meta_boxes', [$this, 'add_metabox']);

        // ajax handler
        add_action('wp_ajax_knife_push_send', [$this, 'send_push']);

        // inject push template
        add_action('wp_footer', [$this, 'inject_template']);

        // update type array by filters
        add_action('init', [$this, 'set_type'], 20);
    }


    /**
     * Update type array by modules filters
     */
    public function set_type() {
       /**
         * Filter push service support post types
         *
         * @since 1.3
         * @param array $type
         */
        $this->type = apply_filters('knife_push_service_type', $this->type);
    }


   /**
    * Enqueue assets to admin post screen only
    */
    public function add_assets($hook) {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), $this->type))
            return;

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_script('knife-push-service', $include . '/scripts/push-service.js', [], $version);
    }


    /**
     * Include app id for OneSignal API to knife-theme js script
     */
    public function inject_object() {
        $opts = get_option($this->option);

        if(empty($opts['appid']) || empty($opts['popup']))
            return false;

        wp_localize_script('knife-theme', 'knife_push_id', $opts['appid']);
    }


    /**
     * Inject push template to footer
     *
     * @since 1.3
     */
    public function inject_template() {
        $opts = get_option($this->option);

        if(empty($opts['appid']) || empty($opts['popup']))
            return false;

        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/push-template.php');
    }


    /**
     * Add push settings submenu to main options menu
     */
    public function add_menu() {
        add_submenu_page('options-general.php', __('Настройки пушей', 'knife-theme'), __('Push-уведомления', 'knife-theme'), 'manage_options', 'knife-push', [$this, 'settings_page']);
    }


    /**
     * Display push options page
     */
     public function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-push-settings');
        do_settings_sections('knife-push-settings');
        submit_button();

        echo '</form>';
    }


    /**
     * Add push sending metabox
     */
    public function add_metabox() {
        add_meta_box('knife-push-metabox', __('Отправить пуш', 'knife-theme'), [$this, 'display_metabox'], $this->type, 'side', 'low');
    }


    /**
     * Display push sending metabox
     */
    public function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/push-metabox.php');
    }


    /**
     * Register settings forms
     */
    public function settings_init() {
        register_setting('knife-push-settings', $this->option);

        add_settings_section(
            'knife-push-section',
            __('Настройки пушей', 'knife-theme'),
            [],
            'knife-push-settings'
        );

        add_settings_field(
            'appid',
            __('OneSignal App ID', 'knife-theme'),
            [$this, 'setting_render_appid'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'rest',
            __('REST API Key', 'knife-theme'),
             [$this, 'setting_render_rest'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'segments',
            __('Сегменты рассылки (через запятую)', 'knife-theme'),
             [$this, 'setting_render_segments'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'title',
            __('Заголовок пуша по умолчанию', 'knife-theme'),
             [$this, 'setting_render_title'],
            'knife-push-settings',
             'knife-push-section'
        );

         add_settings_field(
            'utm',
            __('Параметры ссылки', 'knife-theme'),
             [$this, 'setting_render_utm'],
            'knife-push-settings',
             'knife-push-section'
        );

        add_settings_field(
            'popup',
            __('Показывать popup', 'knife-theme'),
             [$this, 'setting_render_popup'],
            'knife-push-settings',
             'knife-push-section'
        );

    }

    public function setting_render_appid() {
        $options = get_option($this->option);
        $default = isset($options['appid']) ? $options['appid'] : '';

        printf(
            '<input type="text" name="%1$s[appid]" class="widefat" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_rest() {
        $options = get_option($this->option);
        $default = isset($options['rest']) ? $options['rest'] : '';

        printf(
            '<input type="text" name="%1$s[rest]" class="widefat" value="%2$s">',
             $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_segments() {
        $options = get_option($this->option);
        $default = isset($options['segments']) ? $options['segments'] : '';

        printf(
            '<input type="text" name="%1$s[segments]" placeholder="All" class="widefat" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_title() {
        $options = get_option($this->option);
        $default = isset($options['title']) ? $options['title'] : '';

        printf(
            '<input type="text" name="%1$s[title]" class="widefat" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

     public function setting_render_utm() {
        $options = get_option($this->option);
        $default = isset($options['utm']) ? $options['utm'] : '';

        printf(
            '<input type="text" name="%1$s[utm]" placeholder="utm_source=site&utm_medium=webpush" class="widefat" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_popup() {
        $options = get_option($this->option);
        $default = isset($options['popup']) ? $options['popup'] : 0;

        printf(
            '<input type="checkbox" name="%1$s[popup]" value="1" %2$s>',
            $this->option,
            checked($default, 1, false)
        );
    }



    /**
     * Send push using OneSignal API
     */
    public function send_push() {
        $post_id = $_POST['post'];

        if(empty($post_id))
            wp_send_json_error(__("Неверный ID записи", 'knife-theme'));

        $opts = get_option($this->option);

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

        update_post_meta($post_id, $this->meta, $answer->id);

        wp_send_json_success(__("Пуш успешно отправлен", 'knife-theme'));
    }
}
