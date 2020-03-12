<?php
/**
 * Knife-constitution: ajax
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}


if(empty($_REQUEST['text'])) {
    wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));
}

if(method_exists('Knife_Social_Delivery', 'send_telegram')) {
    // Try to find chat in config
    $chat_id = KNIFE_CUSTOMS['knife-constitution']['chat'];

    $message = [
        'text' => wp_unslash($_REQUEST['text']),
        'parse_mode' => 'HTML'
    ];

    $response = Knife_Social_Delivery::send_telegram($chat_id, $message);

    if(!is_wp_error($response)) {
        wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
    }
}

wp_send_json_error(__('Ошибка отправки сообщения. Попробуйте позже', 'knife-theme'));
