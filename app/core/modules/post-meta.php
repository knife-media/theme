<?php
/**
 * Post Meta
 *
 * Return post info meta
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Post_Meta {
    /**
     * Common method to output posts info meta
     */
    public static function get_info($options = [], $output = '') {
        $meta = [];

        // Append promo meta if necessary
        if(method_exists('Knife_Promo_Manager', 'get_info')) {
            $meta[] = Knife_Promo_Manager::get_info($options);
        }

        // Append club meta if necessary
        if(method_exists('Knife_Club_Section', 'get_info')) {
            $meta[] = Knife_Club_Section::get_info($options);
        }

        // Get allowed meta options
        $options = array_intersect($options, [
            'head', 'author', 'date', 'tag', 'tags', 'time', 'category'
        ]);

        foreach($options as $option) {
            $method = 'meta_' . $option;

            if(method_exists(__CLASS__, $method)) {
                $meta[] = self::$method();
            }
        }

        if(array_filter($meta)) {
            $output = $output . sprintf('<div class="meta">%s</div>',
                implode('', $meta)
            );
        }

        return $output;
    }


    /**
     * Get post author info
     */
    private static function meta_author() {
        if(function_exists('coauthors_posts_links')) {
            return coauthors_posts_links('', '', null, null, false);
        }

        return get_the_author_posts_link();
    }


    /**
     * Get post category info
     */
    private static function meta_category() {
        $cats = get_the_category();

        if(empty($cats[0])) {
            return false;
        }

        $output = sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
            esc_html($cats[0]->cat_name),
            esc_url(get_category_link($cats[0]->term_id))
        );

        return $output;
    }


    /**
     * Get post date info
     */
    private static function meta_date() {
        $output = sprintf('<span class="meta__item"><time datetime="%1$s">%2$s</time></span>',
            get_the_time('c'),
            get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
        );

        return $output;
    }


    /**
     * Get post time info
     */
    private static function meta_time() {
        $output = sprintf('<span class="meta__item">%s</span>',
            get_the_time('H:i')
        );

        return $output;
    }


    /**
     * Get primary post tag info
     */
    private static function meta_tag() {
        $tags = get_the_tags();

        if(!isset($tags[0])) {
            return false;
        }

        $output = sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
            esc_html($tags[0]->name),
            esc_url(get_tag_link($tags[0]->term_id))
        );

        return $output;
    }


    /**
     * Get post tags info
     */
    private static function meta_tags($output = '') {
        $tags = get_the_tags();

        if($tags === false) {
            return false;
        }

        foreach($tags as $i => $tag) {
            if($i <= 3) {
                $output = $output . sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
                    esc_html($tag->name),
                    esc_url(get_tag_link($tag->term_id))
                );
            }
        }

        return $output;
    }


    /**
     * Get post head as primary tag
     */
    private static function meta_head() {
        $tags = get_the_tags();

        if(!isset($tags[0])) {
            return false;
        }

        $output = sprintf('<a class="head" href="%2$s">%1$s</a>',
            esc_html($tags[0]->name),
            esc_url(get_tag_link($tags[0]->term_id))
        );

        return $output;
    }


    /**
     * Get question asker
     *
     * @since 1.7
     */
    private static function meta_asker() {
        $options = get_post_meta(get_the_ID(), '_knife-ask-options', true);

        if(empty($options['asker'])) {
            return false;
        }

        $asker = preg_replace('~^([^,]+,?)(.*)$~iu', '<strong>$1</strong>$2', $options['asker']);

        $output = sprintf('<span class="meta__asker">%s</span>',
            strip_tags($asker, '<strong>')
        );

        return $output;
    }


    /**
     * Get question number
     *
     * @since 1.7
     */
    private static function meta_question() {
        $options = get_post_meta(get_the_ID(), '_knife-ask-options', true);

        if(empty($options['counter'])) {
            return false;
        }

        $output = sprintf('<span class="meta__item">%s</span>',
            __('Вопрос ', 'knife-theme') . absint($options['counter'])
        );

        return $output;
    }


    /**
     * Get primary post tag emoji
     *
     * @since 1.7
     */
    private static function meta_emoji() {
        $tags = get_the_tags();

        if(!isset($tags[0])) {
            return false;
        }

        $emoji = get_term_meta($tags[0]->term_id, '_knife-term-emoji', true);

        $output = sprintf('<a class="meta__emoji">%s</a>',
            esc_html($emoji)
        );

        return $output;
    }
}
