<?php
/**
 * Share buttons
 *
 * Share buttons manager class
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.13
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Share_Buttons {
    /**
     * Print social share buttons template
     */
    public static function get_buttons($settings = [], $output = '') {
        // Get default settings
        if(empty($settings)) {
            $settings = self::update_settings();
        }

        foreach($settings as $network => $data) {
            $text = sprintf(
                '<span class="icon icon--%2$s"></span><span class="share__text">%1$s</span>',
                esc_html($data['text']), $network
            );

            $item = sprintf(
                '<a class="share__link share__link--%3$s" href="%2$s" data-label="%3$s" target="_blank">%1$s</a>',
                $text, esc_url($data['link']), esc_attr($network)
            );

            $output = $output . $item;
        }

        return $output;
    }


    /**
     * Return pre-defined buttons settings
     *
     * @since 1.6
     */
    public static function get_settings() {
        $settings = [
            'vkontakte' => [
                'link' => 'https://vk.com/share.php?url=%1$s&title=%2$s',
                'text' => __('Поделиться', 'knife-theme')
            ],

            'facebook' => [
                'link' => 'https://www.facebook.com/sharer/sharer.php?u=%1$s',
                'text' => __('Пошерить', 'knife-theme')
            ],

            'telegram' => [
                'link' => 'https://tgram.link/share/url?url=%1$s&text=%2$s',
                'text' => __('Репостнуть', 'knife-theme')
            ],

            'twitter' => [
                'link' => 'https://twitter.com/intent/tweet?url=%1$s&text=%2$s',
                'text' => __('Твитнуть', 'knife-theme')
            ]
        ];

        return $settings;
    }


    /**
     * Update settings with default data
     *
     * @since 1.12
     */
    private static function update_settings() {
        $settings = self::get_settings();

        foreach($settings as $network => &$data) {
            $title = html_entity_decode(strip_tags(get_the_title()));

            $data['link'] = sprintf($data['link'],
                urlencode(get_permalink()),
                urlencode($title)
            );
        }

        return $settings;
    }
}
