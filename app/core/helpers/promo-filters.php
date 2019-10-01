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
