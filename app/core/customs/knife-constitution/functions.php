<?php

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
        'subject' => [
            'element' => 'input',
            'type' => 'text',
            'required' => '',
            'maxlength' => 100,
            'placeholder' => __('О чем хотите писать', 'knife-theme')
        ],

        'text' => [
            'element' => 'textarea',
            'required' => '',
            'placeholder' => __('Текст поста целиком без форматирования', 'knife-theme')
        ]
    ];

    $options = [
        'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
        'warning' => __('Не удалось отправить заявку. Попробуйте еще раз', 'knife-theme'),
        'button' => __('Отправить', 'knife-theme'),
        'heading' => __('Отправить заявку', 'knife-theme'),
        'action' => $action,
        'fields' => $fields,
        'nonce' => wp_create_nonce($action)
    ];

    // add user form fields
    wp_localize_script('knife-theme', 'knife_form_request', $options);
});
