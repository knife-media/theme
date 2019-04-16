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
     * Option to store telegram bot token
     *
     * @access  private
     * @var     string
     */
    private static $telegram_token = 'knife_notifier_telegram_token';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add notifier required fields to customizer
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);
    }


    /**
     * Save Notifier options
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$telegram_token);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$telegram_token, [
                 'label' => __('Telegram bot token', 'knife-theme'),
                 'section' => 'title_tagline'
             ]
        ));
    }


    /**
     * Send message to Telegram
     */
    public static function send_telegram($message) {
        $token = get_theme_mod(self::$telegram_token);

        if(is_array($message) && strlen($token) > 0) {
            $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
            $response = curl_exec($ch);

            return json_decode($response)->ok;
        }

        return false;
    }
}


/**
 * Load module
 */
Knife_Notifier_Robot::load_module();
