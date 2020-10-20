<?php
/**
 * feedback: custom functions
 *
 * @package knife-theme
 * @since 1.14
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

    if(!defined('KNIFE_REQUESTS')) {
        define('KNIFE_REQUESTS', []);
    }

    // Get secret from config
    $secret = empty(KNIFE_REQUESTS['secret']) ? '' : KNIFE_REQUESTS['secret'];

    // Let's add styles
    wp_enqueue_style('knife-custom-' . $slug, get_template_directory_uri() . $styles, ['knife-theme'], $version);

    // Let's add scripts
    wp_enqueue_script('knife-custom-' . $slug, get_template_directory_uri() . $scripts, ['knife-theme'], $version, true);

    $formats = [
        'custom' => __('Спецпроект с кастомным дизайном', 'knife-theme'),
        'levels' => __('Уровни', 'knife-theme'),
        'cards' => __('Карточки', 'knife-theme'),
        'quiz' => __('Тест', 'knife-theme'),
        'special' => __('Хаб на сайте «Ножа»', 'knife-theme'),
        'longread' => __('Лонгрид', 'knife-theme'),
        'news' => __('Новость', 'knife-theme'),
        'generator' => __('Генератор', 'knife-theme'),
        'social-knife' => __('Проект в соцсетях «Ножа»', 'knife-theme'),
        'flipper' => __('Перевертыши', 'knife-theme'),
        'social-client' => __('Посты в соцсетях клиента', 'knife-theme'),
        'telegram' => __('Пост в telegram', 'knife-theme')
    ];

    $brief = [
        'name' => __('Как называется ваша компания?', 'knife-theme'),
        'site' => __('Сайт компании или продукта', 'knife-theme'),
        'what' => __('Что вы хотите прорекламировать?', 'knife-theme'),
        'purpose' => __('Какова цель рекламной кампании?', 'knife-theme'),
        'users' => __('Кто ваша целевая аудитория?', 'knife-theme'),
        'budget' => __('Какой у вас бюджет?', 'knife-theme'),
        'similar' => __('Какие спецпроекты (в «Ноже» или других медиа) вам нравятся?', 'knife-theme'),
        'contacts' => __('Как вас зовут и как с вами связаться?', 'knife-theme')
    ];

    $callback = [
        'email' => __('Ваша почта', 'knife-theme'),
        'button' => __('Хочу с вами сотрудничать', 'knife-theme'),
    ];

    // Get current time stamp
    $timestamp = time();

    $options = [
        'ajaxurl' => '/requests',
        'nonce' => substr(sha1($secret . $timestamp), -12, 10),
        'time' => $timestamp,
        'button' => __('Отправить', 'knife-theme'),
        'success' => __('Сообщение отправлено', 'knife-theme'),
        'error' => __('Ошибка. Попробуйте позже', 'knife-theme'),
        'figure' => compact('formats', 'brief', 'callback')
    ];

    // add user form fields
    wp_localize_script('knife-custom-' . $slug, 'knife_theme_custom', $options);
});
