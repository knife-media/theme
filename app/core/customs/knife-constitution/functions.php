<?php
/**
 * Knife-constitution: functions
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add form to certain post
 */
add_action('wp_enqueue_scripts', function() {
    // Set current post ajax action
    $action = 'knife-customs-knife-constitution';

    $fields = [
        'text' => [
            'element' => 'textarea',
            'required' => '',
            'placeholder' => __('Я считаю, что обязательно нужно…', 'knife-theme')
        ]
    ];

    $options = [
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'warning' => __('Не удалось отправить предложение. Попробуйте еще раз', 'knife-theme'),
        'button' => __('Отправить', 'knife-theme'),
        'heading' => __('Внести предложение', 'knife-theme'),
        'action' => $action,
        'fields' => $fields,
        'nonce' => wp_create_nonce($action)
    ];

    // add user form fields
    wp_localize_script('knife-theme', 'knife_form_request', $options);
});
