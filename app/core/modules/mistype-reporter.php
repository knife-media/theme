<?php
/**
* Mistype reporter
*
* Send typo errors to telegram private channel
*
* @package knife-theme
* @since 1.12
* @version 1.14
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Mistype_Reporter {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Include mistype reporter settings
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        if(!defined('KNIFE_REQUESTS')) {
            define('KNIFE_REQUESTS', []);
        }
    }


    /**
     * Append user form to page content
     */
    public static function inject_object() {
        $secret = empty(KNIFE_REQUESTS['secret']) ? '' : KNIFE_REQUESTS['secret'];

        // Get current time stamp
        $timestamp = time();

        $options = [
            'ajaxurl' => '/requests',
            'nonce' => substr(sha1($secret . $timestamp), -12, 10),
            'time' => $timestamp,
            'button' => __('Отправить', 'knife-theme'),
            'heading' => __('Сообщить об ошибке', 'knife-theme'),
            'textarea' => __('Комментарий', 'knife-theme')
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_mistype_reporter', $options);
    }
}


/**
 * Load module
 */
Knife_Mistype_Reporter::load_module();
