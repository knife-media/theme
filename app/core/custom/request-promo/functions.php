<?php
/**
 * request-promo: custom functions
 *
 * @package knife-theme
 * @since 1.13
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add custom styles
 */
add_action('wp_enqueue_scripts', function() {
    $slug = basename(__DIR__);

    // Get styles
    $styles = "/core/custom/{$slug}/styles.css";

    // Get scripts
    $scripts = "/core/custom/{$slug}/scripts.js";

    // Set styles version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Let's add styles
    wp_enqueue_style('knife-custom-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);

    // Let's add scripts
    wp_enqueue_script('knife-custom-' . $slug, get_template_directory_uri() . $scripts, ['knife-theme'], $version, true);

    $fields = [
        'name' => [
            'element' => 'input',
            'type' => 'text',
            'required' => '',
            'autocomplete' => 'name',
            'maxlength' => 50,
            'placeholder' => __('Ваше имя, род занятий и проекты', 'knife-theme'),
        ],

        'email' => [
            'element' => 'input',
            'type' => 'email',
            'required' => '',
            'autocomplete' => 'email',
            'maxlength' => 50,
            'placeholder' => __('Электронная почта', 'knife-theme')
        ],

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
        'fields' => $fields,
        'classes' => ['form--club']
    ];

    // add user form fields
    wp_localize_script('knife-theme', 'knife_form_request', $options);
});
