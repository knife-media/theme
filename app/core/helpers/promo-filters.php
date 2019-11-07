<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */


/**
 * HP promo
 * Append link to similar posts via query variable
 */
add_action('wp_enqueue_scripts', function() {
    if(is_singular('post')) {
        $similar_promo = get_query_var('similar_promo', []);

        $similar_promo[] = [
            'title' => 'Пройди тест и выиграй поездку в Амстердам от HP',
            'link' => 'https://knf.md/beyourself-more/',
            'promo' => 1
        ];

        set_query_var('similar_promo', $similar_promo);
    }
}, 9);


/**
 * Honor 9x promo
 * Append link to similar posts via query variable
 */
add_action('wp_enqueue_scripts', function() {
    if(is_singular('post')) {
        $similar_promo = get_query_var('similar_promo', []);

        $similar_promo[] = [
            'title' => 'Супергерой или визионер: <em>кто ты в современном мире</em>',
            'link' => 'https://knf.md/honor9x-more/',
            'promo' => 1
        ];

        set_query_var('similar_promo', $similar_promo);
    }
}, 9);
