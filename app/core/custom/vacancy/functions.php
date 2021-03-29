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
        'error' => __('Ошибка. Попробуйте позже', 'knife-theme'),
        'heading' => esc_html(get_the_title())
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

        // Add mention username
        $options['mention'] = '@current93';
    }

    if($object->post_name === 'native-editor') {
        $fields = [
            'name' => __('Как вас зовут?', 'knife-theme'),
            'occupation' => __('Где и чем вы сейчас занимаетесь?', 'knife-theme'),
            'experince' => __('Какой у вас опыт работы нативредом?', 'knife-theme'),
            'links' => __('Дайте, пожалуйста, ссылки на 3 нативных текста и расскажите о своей роли в них', 'knife-theme'),
            'resume' => __('Расскажите о вашем пуле авторов', 'knife-theme'),
            'subjects' => __('В каких тематиках вы компетентны?', 'knife-theme'),
            'contacts' => __('Ваша почта и мессенджер для оперативной связи', 'knife-theme')
        ];

        $options['fields'] = $fields;

        // Add mention username
        $options['mention'] = '@current93';
    }

    if($object->post_name === 'interviewer') {
        $fields = [
            'name' => __('Как вас зовут?', 'knife-theme'),
            'occupation' => __('Где и чем вы сейчас занимаетесь?', 'knife-theme'),
            'links' => __('Дайте, пожалуйста, ссылки на 3 ваших лучших интервью', 'knife-theme'),
            'subjects' => __('Предложите 3 темы или героя, о которых вы хотите написать для нас', 'knife-theme'),
            'contacts' => __('Ваша почта и мессенджер для оперативной связи', 'knife-theme')
        ];

        $options['fields'] = $fields;

        // Add mention username
        $options['mention'] = '@ArtemChapaev';
    }

    if($object->post_name === 'travel-guide') {
        $fields = [
            'name' => __('Как вас зовут?', 'knife-theme'),
            'occupation' => __('Где и чем вы сейчас занимаетесь?', 'knife-theme'),
            'subject' => __('О каком месте вы хотите нам написать? Когда и сколько вы там жили, что вас связывает с ним?', 'knife-theme'),
            'links' => __('Дайте, пожалуйста, ссылки на 3 ваших лучших текста. Если публикаций в СМИ нет, подойдет длинный пост, по которому можно судить о вашем стиле.', 'knife-theme'),
            'contacts' => __('Ваша почта и мессенджер для оперативной связи', 'knife-theme')
        ];

        $options['fields'] = $fields;

        // Add mention username
        $options['mention'] = '@current93';
    }

    wp_localize_script('knife-custom-' . $slug, 'knife_theme_custom', $options);
});


/**
 * Add list of children pages to vacancy page
 */
add_filter( 'the_content', function( $content ) {
    $pages = [];

    // Get current page object
    $object = get_queried_object();

    $pages[] = sprintf(
        '<a href="%s">%s<span class="icon icon--right"></span></a>',
        esc_url(get_permalink($object->post_parent)),
        __('Просмотреть все вакансии', 'knife-theme')
    );

    if(empty($object->post_parent)) {
        $children = get_posts([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_parent' => $object->ID,
            'posts_per_page' => 20,
            'orderby' => 'menu_order',
            'order' => 'DESC',
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
            // Skip pages with negative order
            if($child->menu_order < 0) {
                continue;
            }

            $pages[] = sprintf(
                '<a href="%s">%s<span class="icon icon--right"></span></a>',
                esc_url(get_permalink($child->ID)),
                get_the_title($child->ID)
            );
        }
    }

    $navigation = sprintf(
        '<figure class="figure figure--navigation">%s</figure>',
        implode('', $pages)
    );

    return $content . $navigation;
} );
