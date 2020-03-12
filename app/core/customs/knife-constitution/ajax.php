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


$fields = [];

foreach(['subject', 'text'] as $key) {
    if(empty($_REQUEST[$key])) {
        wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));
    }

    $fields[$key] = stripslashes_deep($_REQUEST[$key]);
}


if(method_exists('Knife_Social_Delivery', 'send_telegram')) {
    // Try to find chat in config
    $chat_id = KNIFE_CUSTOMS['knife-constitution']['chat'];

    $message = [
        'text' => $fields['text'],
        'parse_mode' => 'HTML'
    ];

    $response = Knife_Social_Delivery::send_telegram($chat_id, $message);

    if(!is_wp_error($response)) {
        wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
    }
}

wp_send_json_error(__('Ошибка отправки сообщения. Попробуйте позже', 'knife-theme'));
