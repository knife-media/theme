<?php
/**
 * Social delivery
 *
 * Send notifications to social networks channels
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Social_Delivery {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Define social delivery settings if still not
        if(!defined('KNIFE_DELIVERY')) {
            define('KNIFE_DELIVERY', []);
        }

        // Die if curl not exists
        if(!function_exists('curl_version')) {
            wp_die(__('Для нормальной работы темы необходимо установить модуль php-curl', 'knife-theme'));
        }
    }


    /**
     * Send message to vk.com
     */
    public static function send_vkontakte($text, $destination, $image = null, $delivery = 'vkontakte') {
    }


    /**
     * Send message to facebook.com
     */
    public static function send_facebook($destination, $message, $delivery = 'facebook') {
        $api = 'https://graph.facebook.com/v3.2/' . $destination;

        // Get delivery settings
        $conf = KNIFE_DELIVERY[$delivery] ?? [];

        // Check token format
        if(!preg_match('~^[0-9a-z]+$~i', $conf['token'])) {
            return new WP_Error('token', __('Неверный формат токена', 'knife-theme'));
        }

        // Append token to message
        $message['access_token'] = $conf['token'];

        // Setup api url
        $endpoint = '/feed';

        // Send message with poster
        if(isset($message['source'])) {
            $message['source'] = new CURLFile($message['source']);

            // Update endpoint
            $endpoint = '/photo';
        }

        return self::make_request($api . $endpoint, $message);
    }


    /**
     * Send message to Telegram
     */
    public static function send_telegram($message, $provider = 'telegram') {
        // Get settings by provider
        $conf = KNIFE_DELIVERY[$provider] ?? [];

        // Check token format
        if(!preg_match('~^[0-9a-z:]+$~i', $conf['token'])) {
            return false;
        }

        if(is_array($message)) {
            $url = 'https://api.telegram.org/bot' . $conf['token'] . '/sendMessage';

            // Send message
            $request = wp_safe_remote_post($url, array(
                'method' => 'POST',
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode($message))
            );

            if(isset($request['body'])) {
                $response = json_decode($request['body']);

                if(isset($response->result->message_id)) {
                    return $response->result->message_id;
                }
            }
        }

        return false;
    }


    /**
     * Send curl request
     */
    private static function make_request($url, $postfields = null) {
        $version = wp_get_theme()->get('Version');

        $options = [
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_USERAGENT => 'knife-theme/' . $version . get_bloginfo('url')
        ];

        if(is_array($postfields)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }

        $handler = curl_init();
        curl_setopt_array($handler, $options);

        return curl_exec($handler);
    }
}


/**
 * Load module
 */
Knife_Social_Delivery::load_module();
