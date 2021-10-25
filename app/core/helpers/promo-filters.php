<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.15
 */

add_action('wp_enqueue_scripts', function() {
    $satanize = [
        'button' => __('Сатанизируй «Нож»', 'knife-theme'),
        'getback' => __('Я передумал, страшно!', 'knife-theme'),
        'process' => __('Подождите…', 'knife-theme'),
    ];

    $posts = [
      'bedridden-diaries',
      'anorexia',
      'fragile-girl',
      'solnechnodolsk-art',
      'butterfly-boy',
      'death-anxiety',
      'zolgensma',
      'trans-mans',
      'everland-interview',
      'muslim-psychology',
      'ussr-lesbians',
      'james-michael-tyler',
      'selling-wife',
      'anti-anxiety-app',
      'rust-kills',
      'psychoactive',
    ];

    if (is_singular()) {
        global $post;

        if(in_array($post->post_name, $posts)) {
            return;
        }
    }

    wp_localize_script('knife-theme', 'knife_satanize', $satanize);
}, 12);

