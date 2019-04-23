<?php
/**
 * Notifier robot
 *
 * Send notifications to private service channels via telegram
 *
 * @package knife-theme
 * @since 1.7
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Notifier_Robot {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Define notifier settings if still not
        if(!defined('KNIFE_NOTIFIER')) {
            define('KNIFE_NOTIFIER', []);
        }
    }


    /**
     * Send message to Telegram
     */
    public static function send_telegram($message, $provider = 'telegram') {
        // Get settings by provider
        $conf = KNIFE_NOTIFIER[$provider] ?? [];

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
}


/**
 * Load module
 */
Knife_Notifier_Robot::load_module();
