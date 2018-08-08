<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can set custom background for certain post here
 *
 * @package knife-theme
 * @since 1.4
 */


/**
 * Skyeng promo
 *
 * Update promo post content with custom button
 */
add_filter('the_content', function($content) {
    if(!is_singular('select') || !in_the_loop()) {
        return $content;
    }

    if(get_post_field('post_name') !== 'study-english') {
        return $content;
    }

    $file = wp_upload_dir();

    $link = sprintf(
        '<a class="outbound outbound--skyeng" href="%2$s" target="_blank"><p class="outbound__promo">%1$s</p><img class="outbound__image" src="%3$s" alt=""></a>',
        __('<strong>Хотите выучить английский, <br>но никак не решитесь? </strong>Skyeng дарит бесплатное первое занятие', 'knife-theme'),
        esc_url('https://skyeng.ru/go/knife'),
        esc_url($file['baseurl'] . '/2018/08/skyeng-logo.png')
    );

    return $content . $link;
});
