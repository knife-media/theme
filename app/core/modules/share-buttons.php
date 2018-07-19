<?php
/**
* Share buttons
*
* Share buttons manager class
*
* @package knife-theme
* @since 1.3
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Share_Buttons;

class Knife_Share_Buttons {
   /**
    * Buttons settings array
    *
    * @access  private
    * @var     array
    */
    private $buttons = [];


    /**
     * Required class pre-options
     */
    public function __construct() {
        $buttons = [
            'vkontakte' => [
                'link' => 'http://vk.com/share.php?url=%1$s&text=%2$s',
                'text' => __('Поделиться', 'knife-theme')
            ],

             'facebook' => [
                'link' => 'http://www.facebook.com/sharer/sharer.php?u=%1$s',
                'text' => __('Пошерить', 'knife-theme')
            ],

            'telegram' => [
                'link' => 'https://t.me/share/url?url=%1$s&text=%2$s',
                'text' => null
            ],

            'twitter' => [
                'link' => 'https://twitter.com/intent/tweet?text=%2$s&url=%1$s',
                'text' => null
            ]
        ];

        /**
         * Filter share buttons settings
         *
         * @param array $buttons
         */
        $this->buttons = apply_filters('knife_share_buttons', $buttons);
    }


    /**
     * Prints social share buttons template
     */
    public function get_buttons($action, $title, $output = '') {
        foreach($this->buttons as $network => $data) {
            $text = sprintf('<span class="icon icon--%s"></span>', $network);

            if($data['text'] !== null)
                $text .= sprintf('<span class="share__text">%s</span>', $data['text']);

            $atts = sprintf(' data-action="%1$s" data-label="%2$s"',
                esc_attr($action),
                esc_attr($network)
            );

            $link = sprintf($data['link'],
                get_permalink(),
                strip_tags(get_the_title())
            );

            $item = sprintf('<a class="share__link share__link--%3$s" href="%2$s" target="_blank"%4$s>%1$s</a>',
                $text, esc_url($link), esc_attr($network), $atts
            );

            $output = $output . $item;
        }

        if(strlen($title) > 0) {
            return sprintf('<div class="share__title">%s</div>', $title) . $output;
        }

        return $output;
    }
}
