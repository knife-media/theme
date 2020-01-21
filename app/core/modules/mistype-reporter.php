<?php
/**
* Mistype reporter
*
* Send typo errors to telegram private channel
*
* @package knife-theme
* @since 1.12
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Mistype_Reporter {
    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_request = 'knife-mistype-request';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Send mistype error
        add_action('wp_ajax_' . self::$ajax_request, [__CLASS__, 'submit_error']);
        add_action('wp_ajax_nopriv_' . self::$ajax_request, [__CLASS__, 'submit_error']);

        // Include mistype reporter settings
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Define mistype settings if still not
        if(!defined('KNIFE_MISTYPE')) {
            define('KNIFE_MISTYPE', []);
        }
    }


    /**
     * Append user form to page content
     */
    public static function inject_object() {
        if(empty(KNIFE_MISTYPE['chat'])) {
            return;
        }

        $options = [
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'button' => __('Отправить', 'knife-theme'),
            'heading' => __('Сообщить об ошибке', 'knife-theme'),
            'textarea' => __('Комментарий', 'knife-theme'),
            'action' => self::$ajax_request,
            'nonce' => wp_create_nonce(self::$ajax_request)
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_mistype_reporter', $options);
    }


    /**
     * Send mustype error to telegram
     */
    public static function submit_error() {
        if(!check_ajax_referer(self::$ajax_request, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }


        if(method_exists('Knife_Social_Delivery', 'send_telegram')) {
            // Try to find chat in config
            $chat_id = KNIFE_MISTYPE['chat'] ?? '';

            $message = [
                'text' => self::get_message(wp_unslash($_REQUEST)),
                'parse_mode' => 'HTML'
            ];

            // Don't need to process errors now
            $response = Knife_Social_Delivery::send_telegram($chat_id, $message);
        }
    }


    /**
     * Get message from request fields
     */
    private static function get_message($fields) {
        $fields = wp_parse_args($fields, [
            'marked' => '',
            'location' => ''
        ]);

        $text = sprintf("%s \n%s \n\n%s \n",
            __('<strong>Добавлено сообщение об ошибкe</strong>', 'knife-theme'),
            esc_html($fields['marked']), esc_html($fields['location'])
        );

        // Add comment if not empty
        if(!empty($fields['comment'])) {
            $comment = sprintf(
                __('<strong>Комментарий:</strong> %s', 'knife-theme'),
                esc_html(stripslashes($fields['comment']))
            );

            $text = $text . $comment;
        }

        return $text;
    }
}


/**
 * Load module
 */
Knife_Mistype_Reporter::load_module();
