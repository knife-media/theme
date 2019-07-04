<?php
/**
 * Post info
 *
 * Return post info data
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Post_Info {
    /**
     * Post info entry point
     *
     * @since 1.8
     */
    public static function get_info($options = [], $output = '') {
        $stamp = [];

        // Get club about info
        if(in_array('club', $options)) {
            $stamp[] = self::get_club($output);
        }

        // Get post meta
        $output = self::get_meta($options, $output);

        // Wrap about if exists
        if(array_filter($stamp)) {
            $output = sprintf('<div class="info__wrapper">%s</div>',
                implode('', $stamp) . $output
            );
        }

        // Get head info
        if(in_array('head', $options)) {
            $output = self::get_head($output);
        }

        // Get label info
        if(in_array('label', $options)) {
            $output = self::get_label($output);
        }

        // Get asker info
        if(in_array('asker', $options)) {
            $output = self::get_asker($output);
        }

        return $output;
    }


    /**
     * Get club about meta
     *
     * @since 1.8
     */
    private static function get_club($output = '') {
        $post_type = get_post_type(get_the_id());

        if($post_type === 'club') {
            $output = $output . sprintf('<a class="stamp stamp--club" href="%2$s">%1$s</a>',
                esc_html(get_post_type_object($post_type)->labels->name),
                esc_url(get_post_type_archive_link($post_type))
            );
        }

        return $output;
    }


    /**
     * Get akser info meta
     *
     * @since 1.8
     */
    private static function get_asker($output = '') {
        $options = get_post_meta(get_the_ID(), '_knife-ask-options', true);

        if(!empty($options['asker'])) {
            $output = $output . preg_replace('~^([^,]+,?)(.*)$~iu', '<strong>$1</strong>$2',
                strip_tags($options['asker'])
            );
        }

        return $output;
    }


    /**
     * Get label info meta
     *
     * @since 1.8
     */
    private static function get_label($output = '') {
        $labels = get_the_terms(get_the_ID(), 'label');

        // Check if labels exist
        if(is_array($labels)) {
            $links = [];

            foreach($labels as $label) {
                $emoji = get_term_meta($label->term_id, '_knife-term-emoji', true);

                $links[] = sprintf('<a class="label__link" href="%2$s" title="%3$s">%1$s</a>',
                    esc_html($emoji),
                    esc_url(get_term_link($label->term_id)),
                    sanitize_text_field($label->name)
                );
            }

            $output = $output . sprintf('<div class="label">%s</div>',
                implode('', $links)
            );
        }

        return $output;
    }


    /**
     * Get head info meta
     *
     * @since 1.8
     */
    private static function get_head($output = '') {
        $is_promo = get_post_meta(get_the_ID(), '_knife-promo', true);

        if($is_promo) {
            $output = sprintf('<a class="head" href="%s">%s</a>',
                esc_url(home_url('/promo/')),
                __('Партнерский материал', 'knife-theme')
            );

            return $output;
        }

        $tags = get_the_tags();

        if(isset($tags[0])) {
            $output = $output . sprintf('<a class="head" href="%2$s">%1$s</a>',
                esc_html($tags[0]->name),
                esc_url(get_tag_link($tags[0]->term_id))
            );
        }

        return $output;
    }


    /**
     * Common method to output posts info meta
     *
     * @since 1.8
     */
    private static function get_meta($options = [], $output = '') {
        // Get allowed meta options
        $options = array_intersect($options, [
            'author', 'date', 'tag', 'tags', 'time', 'category', 'emoji', 'question'
        ]);

        $meta = [];

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
    private static function meta_category($output = '') {
        $cats = get_the_category();

        if(isset($cats[0])) {
            $output = sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
                esc_html($cats[0]->cat_name),
                esc_url(get_category_link($cats[0]->term_id))
            );
        }

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
    private static function meta_tag($output = '') {
        $is_promo = get_post_meta(get_the_ID(), '_knife-promo', true);

        if($is_promo) {
            $output = sprintf('<a class="meta__item" href="%s">%s</a>',
                esc_url(home_url('/promo/')),
                __('Партнерский материал', 'knife-theme')
            );

            return $output;
        }

        $tags = get_the_tags();

        if(isset($tags[0])) {
            $output = sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
                esc_html($tags[0]->name),
                esc_url(get_tag_link($tags[0]->term_id))
            );
        }

        return $output;
    }


    /**
     * Get post tags info
     */
    private static function meta_tags($output = '') {
        $tags = get_the_tags();

        if(is_array($tags)) {
            foreach($tags as $i => $tag) {
                if($i <= 3) {
                    $output = $output . sprintf('<a class="meta__item" href="%2$s">%1$s</a>',
                        esc_html($tag->name),
                        esc_url(get_tag_link($tag->term_id))
                    );
                }
            }
        }

        return $output;
    }


    /**
     * Get question number
     *
     * @since 1.7
     */
    private static function meta_question($output = '') {
        $options = get_post_meta(get_the_ID(), '_knife-ask-options', true);

        if(isset($options['counter'])) {
            $output = sprintf('<span class="meta__item">%s</span>',
                __('Вопрос ', 'knife-theme') . absint($options['counter'])
            );
        }

        return $output;
    }


    /**
     * Get primary post tag emoji
     *
     * @since 1.7
     */
    private static function meta_emoji($output = '') {
        $tags = get_the_tags();

        if(isset($tags[0])) {
            $emoji = get_term_meta($tags[0]->term_id, '_knife-term-emoji', true);

            $output = sprintf('<a class="meta__emoji">%s</a>',
                esc_html($emoji)
            );
        }

        return $output;
    }
}
