<?php
/**
 * Volnoe delo: functions
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}


/**
 * Add button to post content
 */
add_action('the_content', function($content) {
    global $post;

    if(has_term('volnoe-delo', 'special', $post) && in_the_loop()) {
        $promo_link = sprintf('<figure class="figure figure--promo"><a class="button" href="%s"><strong>%s</strong> %s</a>',
            esc_url(get_term_link('volnoe-delo', 'special')),
            _x('Специальный проект', 'special: volnoe delo', 'knife-theme'),
            _x('Фонда «Вольное дело» Дерипаски и Журнала «Нож»', 'special: volnoe delo', 'knife-theme')
        );

        $content = $content . $promo_link;
    }

    return $content;
});