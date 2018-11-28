<?php
/**
* Share buttons
*
* Share buttons manager class
*
* @package knife-theme
* @since 1.3
* @version 1.6
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Share_Buttons {
    /**
     * Print social share buttons template
     */
    public static function get_buttons($action, $output = '') {
        $settings = self::get_settings();

        foreach($settings as $network => $data) {
            $text = sprintf(
                '<span class="icon icon--%2$s"></span><span class="share__text">%1$s</span>',
                esc_html($data['text']), $network
            );

            $atts = sprintf(' data-action="%1$s" data-label="%2$s"',
                esc_attr($action),
                esc_attr($network)
            );

            $link = sprintf($data['link'],
                esc_url(get_permalink()),
                urlencode(strip_tags(get_the_title()))
            );

            $item = sprintf('<a class="share__link share__link--%3$s" href="%2$s" target="_blank"%4$s>%1$s</a>',
                $text, esc_url($link), esc_attr($network), $atts
            );

            $output = $output . $item;
        }

        return $output;
    }


    /**
     * Return social share buttons object
     *
     * @since 1.6
     */
    public static function get_object($post_id, $action, $object = []) {
        $object = [
            'url' => get_permalink($post_id),
            'action' => esc_attr($action),
            'settings' => self::get_settings()
        ];

        return $object;
    }


    /**
     * Return pre-defined buttons settings
     *
     * @since 1.6
     */
    private static function get_settings() {
        $settings = [
            'vkontakte' => [
                'link' => 'https://vk.com/share.php?url=%1$s&text=%2$s',
                'text' => __('Поделиться', 'knife-theme')
            ],

            'facebook' => [
                'link' => 'https://www.facebook.com/sharer/sharer.php?u=%1$s',
                'text' => __('Пошерить', 'knife-theme')
            ],

            'telegram' => [
                'link' => 'https://t.me/share/url?url=%1$s&text=%2$s',
                'text' => __('Репостнуть', 'knife-theme')
            ],

            'twitter' => [
                'link' => 'https://twitter.com/intent/tweet?url=%1$s&text=%2$s',
                'text' => __('Твитнуть', 'knife-theme')
            ]
        ];

        return $settings;
    }
}
