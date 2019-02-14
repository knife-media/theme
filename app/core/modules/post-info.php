<?php
/**
 * Post Info
 *
 * Return post info meta
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.5
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Post_Info {
    /**
     * Common method to output posts info meta
     */
    public static function get_info($options = [], $output = '') {
        $meta = array_intersect($options, ['club', 'author', 'date', 'tag', 'tags', 'time', 'category', 'asker']);
        $items = [];

        foreach($meta as $option) {
            $method = 'info_' . $option;

            if(method_exists(__CLASS__, $method)) {
                $items[] = self::$method();
            }
        }

        if(count($items) > 0) {
            $output = $output . sprintf('<div class="meta">%s</div>',
                implode('', $items)
            );
        }

        foreach(array_diff($options, $meta) as $option) {
            $method = 'info_' . $option;

            if(method_exists(__CLASS__, $method)) {
                $output = $output . self::$method();
            }
        }

        return $output;
    }


    /**
     * Get post author info
     */
    private static function info_author() {
        if(function_exists('coauthors_posts_links')) {
            return coauthors_posts_links('', '', null, null, false);
        }

        return get_the_author_posts_link();
    }


    /**
     * Get post category info
     */
    private static function info_category() {
        $cats = get_the_category();

        if(empty($cats[0])) {
            return false;
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
    private static function info_date() {
        $output = sprintf('<span class="meta__item"><time datetime="%1$s">%2$s</time></span>',
            get_the_time('c'),
            get_the_date('Y') === date('Y') ? get_the_time('j F') : get_the_time('j F Y')
        );

        return $output;
    }


    /**
     * Get post time info
     */
    private static function info_time() {
        $output = sprintf('<span class="meta__item">%s</span>',
            get_the_time('H:i')
        );

        return $output;
    }


    /**
     * Get primary post tag info
     */
    private static function info_tag() {
        $tags = get_the_tags();

        if(!isset($tags[0])) {
            return false;
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
    private static function info_tags($output = '') {
        $tags = get_the_tags();

        if($tags === false) {
            return false;
        }

        foreach($tags as $i => $tag) {
            if($i <= 3) {
                $output = $output . sprintf('<a class="meta__link" href="%2$s">%1$s</a>',
                    esc_html($tag->name),
                    esc_url(get_tag_link($tag->term_id))
                );
            }
        }

        return $output;
    }


    /**
     * Get post type info
     */
    private static function info_club() {
        $type = get_post_type(get_the_ID());

        if($type !== 'club') {
            return false;
        }

        $output = sprintf('<a class="meta__club" href="%2$s">%1$s</a>',
            esc_html(get_post_type_object($type)->labels->name),
            esc_url(get_post_type_archive_link($type))
        );

        return $output;
    }


    /**
     * Get post head as primary tag
     */
    private static function info_head() {
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
     * Get post labels
     */
    private static function info_label() {
        $labels = get_the_terms(get_the_ID(), 'label');

        if($labels === false) {
            return false;
        }

        $links = [];

        foreach($labels as $label) {
            $emoji = get_term_meta($label->term_id, '_knife-term-emoji', true);

            $links[] = sprintf('<a class="label__link" href="%2$s" title="%3$s">%1$s</a>',
                esc_html($emoji),
                esc_url(get_term_link($label->term_id)),
                sanitize_text_field($label->name)
            );
        }

        $output = sprintf('<div class="label">%s</div>',
            implode('', $links)
        );

        return $output;
    }


    /**
     * Get question asker
     */
    private static function info_asker() {
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
}
