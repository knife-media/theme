<?php
/**
* Common template tags
*
* Useful template manager class for
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
        add_filter('the_tags', [$this, 'tags'], 10, 1);
    }


    /**
     * Prints current post tags list
     */
    public function tags($tags) {
        return str_replace('href="', 'class="refers__link" href="', $tags);
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

    public function meta($args, $html = '') {
        $defaults = [
            'opts' => ['author', 'date', 'category'],
            'before' => '',
            'after' => '',
            'item' => '<span class="meta__item">%s</span>',
            'link' => '<a class="meta__link" href="%2$s">%1$s</a>',
            'is_link' => true,
            'echo' => true
        ];

        $args = wp_parse_args($args, $defaults);

        foreach($args['opts'] as $item) {
            switch($item) {

                // Prints post author
                case 'author':
                    if($args['is_link'] === false) :
                        if(function_exists('coauthors'))
                            $html .= sprintf($args['item'], coauthors('', '', null, null, false));
                        else
                            $html .= sprintf($args['item'], get_the_author());
                    else :
                        if(function_exists('coauthors_posts_links'))
                            $html .= coauthors_posts_links('', '', null, null, false);
                        else
                            $html .= get_the_author_posts_link();
                    endif;
                break;

                // Prints post category
                case 'category':
                    $cats = get_the_category();

                    if(!isset($cats[0]))
                        break;

                    if($args['is_link'] === false) :
                        $html .= sprintf($args['item'], sanitize_text_field($cats[0]->cat_name));
                    else :
                        $html .= sprintf($args['link'],
                            sanitize_text_field($cats[0]->cat_name),
                            esc_url(get_category_link($cats[0]->term_id))
                        );
                    endif;
                break;

                // Prints post publish date. Exclude current year
                case 'date':
                    $date = sprintf('<time datetime="%1$s">%2$s</time>',
                        get_the_time('c'),
                        get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
                    );

                     $html .= sprintf($args['item'], $date);
                break;

                // Prints only post publish time. Useful for news
                case 'time':
                     $html .= sprintf($args['item'], get_the_time('H:i'));
                break;

                //  Prints post single tag
                case 'tag' :
                    if($args['is_link'] === false) :
                        $html .= knife_theme_tags(['item' => '%1$s', 'count' => 1, 'echo' => false]);
                    else :
                        $html .= knife_theme_tags(['item' => $args['link'], 'count' => 1, 'echo' => false]);
                    endif;
                break;

                // Same as above but for 3 tags
                case 'tags' :
                    if($args['is_link'] === false) :
                        $html .= knife_theme_tags(['item' => '%1$s', 'count' => 3, 'echo' => false, 'between' => '']);
                    else :
                        $html .= knife_theme_tags(['item' => $args['link'], 'count' => 3, 'echo' => false, 'between' => '']);
                    endif;
                break;

                // Show widget head using widget query vars
                case 'head' :
                    $title = get_query_var('widget_title', '');
                    $link = get_query_var('widget_link', '');

                    if(empty($title))
                        break;

                    if(empty($link)) :
                        $html .= sprintf($args['item'], sanitize_text_field($title));
                    else:
                        $html .= sprintf($args['link'], sanitize_text_field($title), esc_url($link));
                    endif;
                break;

                // Show post type badge if exists
                case 'type' :
                    $inherit = ['post', 'page', 'attachmenet'];
                    $type = get_post_type(get_the_ID());

                    if(in_array($type, $inherit))
                        break;

                    $html .= sprintf('<a class="meta__link meta__link--%3$s" href="%2$s">%1$s</a>',
                        sanitize_text_field(get_post_type_object($type)->labels->name),
                        esc_url(get_post_type_archive_link($type)),
                        esc_attr($type)
                    );
                break;
            }
        }

        $html = $args['before'] . $html . $args['after'];

          // Filter result html before return
        $html = apply_filters('knife_theme_meta', $html, $args);

        if($args['echo'] === false)
            return $html;

        echo $html;

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

if(!function_exists('the_share')) :
    /**
     * Public function using on templates to get current post lead text
     */
    function the_share($before = '', $after = '', $options = '', $echo = true) {
        $share = (new Knife_Template_Tags)->share($action, $title);

        $output = $before . $share . $after;

        if($echo === true)
            echo $output;

        return $output;
    }
endif;
