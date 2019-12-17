<?php
/**
 * Post info
 *
 * Return post info data
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.10
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

        return $output;
    }


    /**
     * Get entry caption
     *
     * @since 1.10
     */
    public static function get_tagline($output = '') {
        $post_id = get_the_ID();

        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if(get_post_meta($post_id, $meta_promo, true)) {
                return self::compose_promo_tagline($post_id, $output);
            }
        }

        // Check special projects class and return template
        if(class_exists('Knife_Special_Projects')) {
            $taxonomy = Knife_Special_Projects::$taxonomy;

            if(has_term('', $taxonomy, $post_id)) {
                return self::compose_special_tagline($post_id, $output);
            }
        }

        return $output;
    }


    /**
     * Get club about meta
     *
     * @since 1.8
     */
    private static function get_club($output = '') {
        $post_type = get_post_type(get_the_ID());

        if($post_type === 'club') {
            $output = $output . sprintf('<a class="stamp stamp--club" href="%2$s">%1$s</a>',
                esc_html(get_post_type_object($post_type)->labels->name),
                esc_url(get_post_type_archive_link($post_type))
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
        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            // Check if promo first
            if(get_post_meta(get_the_ID(), $meta_promo, true)) {
                $output = sprintf('<a class="head" href="%s">%s</a>',
                    trailingslashit(home_url(Knife_Promo_Manager::$query_var)),
                    __('Партнерский материал', 'knife-theme')
                );

                return $output;
            }
        }

        if(class_exists('Knife_Special_Projects')) {
            $terms = get_the_terms(get_the_ID(), Knife_Special_Projects::$taxonomy);

            // Check if has terms next
            if(isset($terms[0])) {
                $output = $output . sprintf('<a class="head" href="%2$s">%1$s</a>',
                    esc_html($terms[0]->name),
                    esc_url(get_term_link($terms[0]->term_id))
                );

                return $output;
            }
        }

        $tags = get_the_tags();

        // Check for tags finally
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
            'author', 'date', 'tag', 'tags', 'time', 'category', 'emoji'
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


    /**
     * Compose promo tagline
     *
     * @since 1.10
     */
    private static function compose_promo_tagline($post_id, $output = '') {
        $classes = 'tagline';

        // Get promo options
        $options = (array) get_post_meta($post_id, Knife_Promo_Manager::$meta_options, true);

        // Set default promo panel color
        if(empty($options['color'])) {
            $options['color'] = '#fff';
        }

        $partner = '';

        // Add logo if exists
        if(!empty($options['logo'])) {
            $partner = $partner . sprintf(
                '<img class="tagline__partner-logo" src="%s" alt="">',
                esc_url($options['logo'])
            );

            $classes = $classes . ' tagline--logo';
        }

        // Add title if exists
        if(!empty($options['title'])) {
            $partner = $partner . sprintf(
                '<span class="tagline__partner-title">%s</span>',
                sanitize_text_field($options['title'])
            );

            $classes = $classes . ' tagline--title';
        }

        // Add required title
        if(empty($options['text'])) {
            $options['text'] = __('Партнерский материал', 'knife-theme');
        }

        $promo = sprintf(
            '<span class="tagline__text">%s</span>',
            sanitize_text_field($options['text'])
        );

        // Wrap logo and title
        if(!empty($partner)) {
            $promo = $promo . sprintf(
                '<div class="tagline__partner">%s</div>', $partner
            );
        }

        $styles = [
            'background-color:' . $options['color'],
            'color:' . self::get_text_color($options['color'])
        ];

        $styles = implode('; ', $styles);

        // Return if link not defined
        if(empty($options['link'])) {
            $output = sprintf(
                '<div class="%2$s" style="%3$s">%1$s</div>',
                $promo, $classes, esc_attr($styles)
            );

            return $output;
        }

        $output = sprintf(
            '<a href="%2$s" class="%3$s" target="_blank" rel="noopener" style="%4$s">%1$s</a>',
            $promo, esc_url($options['link']),
            $classes, esc_attr($styles)
        );

        return $output;
    }


    /**
     * Compose special tagline
     *
     * @since 1.10
     */
    private static function compose_special_tagline($post_id, $output = '') {
        $taxonomy = Knife_Special_Projects::$taxonomy;

        // Loop over all tax terms
        foreach(get_the_terms($post_id, $taxonomy) as $term) {
            $ancestors = get_ancestors($term->term_id, $taxonomy, 'taxonomy');

            // Get parent if exists
            if(!empty($ancestors)) {
                $term = get_term($ancestors[0], $taxonomy);
            }

            $options = get_term_meta($term->term_id, Knife_Special_Projects::$term_meta, true);

            if(empty($options['single'])) {
                $output = sprintf(
                    '<a class="tagline" href="%2$s"><span class="tagline__name">%1$s</span></a>',
                    esc_html($term->name),
                    esc_url(get_term_link($term->term_id))
                );

                break;
            }

            $styles = [
                'background:' . $options['single'],
                'color:' . self::get_text_color($options['single'])
            ];

            $output = sprintf(
                '<a class="tagline" href="%2$s" style="%3$s"><span class="tagline__name">%1$s</span></a>',
                esc_html($term->name),
                esc_url(get_term_link($term->term_id)),
                esc_attr(implode('; ', $styles))
            );

            break;
        }

        return $output;
    }


    /**
     * Get text color using relative luminance
     *
     * @link https://en.wikipedia.org/wiki/Relative_luminance
     * @since 1.10
     */
    private static function get_text_color($color) {
        $color = trim($color, '#');

        if(strlen($color) == 3) {
            $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
            $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
            $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
        } elseif(strlen($color) == 6) {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        }

        // Get relative luminance
        $y = 0.2126*$r + 0.7152*$g + 0.0722*$b;

        return $y > 128 ? '#000' : '#fff';
    }
}
