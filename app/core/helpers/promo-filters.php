<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.10
 */


/**
 * Yandex.Taxi promo
 * Append link to similar posts via query variable
 */
add_action('wp_enqueue_scripts', function() {
    if(is_singular('post')) {
        $similar_promo = get_query_var('similar_promo', []);

        $similar_promo[] = [
            'title' => 'Почему горожане отказываются от личного авто? <em>Читай 10 историй</em>',
            'link' => 'https://knf.md/yandextaxi-recomendation/',
            'promo' => 1
        ];

        set_query_var('similar_promo', $similar_promo);
    }
}, 9);


/**
 * Telegram promo
 * Add telegram internal promo button to news
 */
add_filter('the_content', function($content) {
    if(!is_single() || !in_the_loop()) {
        return $content;
    }

    if(!in_category('news') || has_post_format()) {
        return $content;
    }

    $upload_dir = wp_upload_dir();

    $promo_link = sprintf(
        '<a class="promo promo--possum" href="%2$s" target="_blank">%1$s <img src="%3$s"></a>',
        __('Подпишись на&nbsp;журнал «Нож» в&nbsp;Телеграме', 'knife-theme'),
        esc_url('https://knf.md/knife-telegram/'),
        esc_url($upload_dir['baseurl'] . '/2019/10/possum.png')
    );

    return $content . $promo_link;
});
