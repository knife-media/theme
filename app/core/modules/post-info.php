<?php
/**
* Post Info
*
* Return post info meta
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Post_Info {
    /**
     * Common method to output posts info meta
     */
    public static function get_info($options = [], $output = '') {
        foreach($options as $option) {
            $method = 'info_' . $option;

            if(!method_exists(__CLASS__, $method)) {
                continue;
            }

            $output = $output . self::$method();
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
     * Get post type info
     */
    private static function info_type() {
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
    private static function info_tag() {
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
}
