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
    }


    /**
     * Send message to vk.com
     */
    public static function send_vkontakte($text, $destination, $image = null, $delivery = 'vkontakte') {
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
    private static function make_request() {
    }
}


/**
 * Load module
 */
Knife_Social_Delivery::load_module();
