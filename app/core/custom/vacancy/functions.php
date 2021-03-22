<?php
/**
 * vacancy: custom functions
 *
 * @package knife-theme
 * @since 1.15
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add custom assets
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

    // Get current time stamp
    $timestamp = time();

    $options = [
        'ajaxurl' => '/requests/vacancy/',
        'nonce' => substr(sha1($secret . $timestamp), -12, 10),
        'time' => $timestamp,
        'button' => __('Отправить', 'knife-theme'),
        'success' => __('Сообщение отправлено', 'knife-theme'),
        'error' => __('Ошибка. Попробуйте позже', 'knife-theme')
    ];

    $object = get_queried_object();

    if($object->post_name === 'explainer') {
        $fields = [
            'name' => __('Как вас зовут?', 'knife-theme'),
            'occupation' => __('Где и чем вы сейчас занимаетесь?', 'knife-theme'),
            'experince' => __('Какой у вас опыт работы, релевантный этой вакансии?', 'knife-theme'),
            'links' => __('Дайте, пожалуйста, ссылки на 3 ваших лучших текста', 'knife-theme'),
            'subjects' => __('Придумайте 5 тем для «Ножа», которые будут популярны в поиске', 'knife-theme'),
            'contacts' => __('Ваша почта и мессенджер для оперативной связи', 'knife-theme')
        ];

        $options['fields'] = $fields;
    }

    // add user form fields
    wp_localize_script('knife-custom-' . $slug, 'knife_theme_custom', $options);
});


/**
 * Add list of children pages to vacancy page
 */
add_filter( 'the_content', function( $content ) {
    $object = get_queried_object();

    // Skip non root page.
    if($object->post_name !== 'vacancy') {
        return $content;
    }

    $children = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'post_parent' => $object->ID,
        'fields' => 'ids'
    ]);

    if(empty($children)) {
        $message = sprintf(
            '<h3><strong>%s</strong></h3>',
            __('Актуальных вакасний пока нет', 'knife-theme')
        );

        return $content . $message;
    }

    $pages = [];

    foreach($children as $child) {
        $pages[] = sprintf(
            '<a href="%s">%s<span class="icon icon--right"></span></a>',
            esc_url(get_permalink($child)),
            get_the_title($child)
        );
    }

    $navigation = sprintf(
        '<figure class="figure--navigation">%s</figure>',
        implode('', $pages)
    );

    return $content . $navigation;
} );
