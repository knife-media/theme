<?php
/**
* Common template tags
*
* Useful template manager class
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

(new Knife_Template_Tags)->init();

class Knife_Template_Tags {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public function init() {
        // Add navigation on archive pages
        add_action('loop_end', [$this, 'archive_navigation']);


        // Fix the_tags output
        add_filter('the_tags', function($tags) {
            return str_replace('href="', 'class="refers__link" href="', $tags);
        }, 10, 1);


        // Remove useless image attributes
        add_filter('post_thumbnail_html', function($html) {
            return preg_replace('/(width|height)="\d*"\s/', "", $html);
        }, 10);

        add_filter('get_image_tag', function($html) {
            return preg_replace('/(width|height)="\d*"\s/', "", $html);
        }, 10);

        add_filter('get_image_tag_class', function($class, $id, $align, $size) {
            $class = 'figure__image';

            return $class;
        }, 0, 4);

    }


    /**
     * Prints navigation link if needed
     */
    public function archive_navigation($query) {
        if($query->is_archive() && get_next_posts_link()) {
            next_posts_link(__('Больше статей', 'knife-theme'));
        }
    }


    /**
     * Prints social share buttons template
     */
    public function share($action, $title, $output = '') {
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

        foreach($buttons as $network => $data) {
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

        if(strlen($title) > 0)
            return sprintf('<div class="share__title">%s</div>', $title) . $output;

        return $output;
    }

    public function info($options, $output = '') {
        foreach($options as $option) {
            $method = 'info_' . $option;

            if(!method_exists(__CLASS__, $method))
                continue;

            $output = $output . $this->$method();
        }

        return $output;
    }


    /**
     * Get post author info
     */
    private function info_author() {
        if(function_exists('coauthors_posts_links')) {
            return coauthors_posts_links('', '', null, null, false);
        }

        return get_the_author_posts_link();
    }


    /**
     * Get post category info
     */
    private function info_category() {
        $cats = get_the_category();

        if(!isset($cats[0])) {
            return;
        }

        $output = sprintf('<a class="meta__link" href="%2$s">%1$s</a>',
            esc_html($cats[0]->cat_name),
            esc_url(get_category_link($cats[0]->term_id))
        );

        return $output;
    }


    /**
     * Get post date info
     */
    private function info_date() {
        $output = sprintf('<span class="meta__item"><time datetime="%1$s">%2$s</time></span>',
            get_the_time('c'),
            get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
        );

        return $output;
    }


    /**
     * Get post time info
     */
    private function info_time() {
        $output = sprintf('<span class="meta__item">%s</span>',
            get_the_time('H:i')
        );

        return $output;
    }


    /**
     * Get post type info
     */
    private function info_type() {
        $type = get_post_type(get_the_ID());

        if(in_array($type, ['post', 'page', 'attachmenet'])) {
            return;
        }

        $output = sprintf('<a class="meta__link meta__link--%3$s" href="%2$s">%1$s</a>',
            esc_html(get_post_type_object($type)->labels->name),
            esc_url(get_post_type_archive_link($type)),
            esc_attr($type)
        );

        return $output;
    }


    /**
     * Get primary post tag info
     */
    private function info_tag() {
        $tags = get_the_tags();

        if(!isset($tags[0])) {
            return;
        }

        $output = sprintf('<a class="meta__link" href="%2$s">%1$s</a>',
            esc_html($tags[0]->name),
            esc_url(get_tag_link($tags[0]->term_id))
        );

        return $output;
    }


    /**
     * Get post tags info
     */
    private function info_tags($output = '') {
        $tags = get_the_tags();

        if($tags === false) {
            return false;
        }

        foreach($tags as $i => $tag) {
            if($i > 3)
                break;

            $output = $output . sprintf('<a class="meta__link" href="%2$s">%1$s</a>',
                esc_html($tag->name),
                esc_url(get_tag_link($tag->term_id))
            );
        }

        return $output;
    }
}

if(!function_exists('the_share')) :
    /**
     * Public function using on templates to get current post lead text
     */
    function the_share($before = '', $after = '', $action = '', $title = '',  $echo = true) {
        $share = (new Knife_Template_Tags)->share($action, $title);

        $output = $before . $share . $after;

        if($echo === true)
            echo $output;

        return $output;
    }
endif;

if(!function_exists('the_info')) :
    /**
     * Public function using on templates to get current post lead text
     */
    function the_info($before = '', $after = '', $options = '', $echo = true) {
        $info= (new Knife_Template_Tags)->info($options);

        $output = $before . $info . $after;

        if($echo === true)
            echo $output;

        return $output;
    }
endif;

function knife_theme_meta() {
}
