<?php
/**
* Common template handler
*
* Useful template manager class
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

(new Knife_Template_Handler)->init();

class Knife_Template_Handler {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public function init() {
        // Add widget size query var
        add_action('the_post', [$this, 'archive_item'], 10, 2);

        // Add navigation on archive pages
        add_action('loop_end', [$this, 'archive_more']);

        // Add archive header
        add_action('knife_template', [$this, 'archive_header']);
    }


    /**
     * Add widget size query var on archive loops
     */
    public function archive_item($post, $query) {
        $size = function($current, $found) use (&$args) {
            if($found < 3 || $current % 5 === 3 || $current % 5 === 4)
                return 'double';

            return 'triple';
        };

        if($query->is_archive() && $query->is_main_query()) {
            set_query_var('widget_size', $size($query->current_post, (int) $query->found_posts));
        }
    }


    /**
     * Prints navigation link if needed
     */
    public function archive_more($query) {
        if($query->is_archive() && get_next_posts_link()) {
            $more = next_posts_link(__('Больше статей', 'knife-theme'));

            /**
             * Filter archive more link
             *
             * @since 1.3
             * @param string $more
             */
            echo apply_filters('knife_archive_more', $more);
        }
    }


    /**
     * Append archive header including title and description
     */
    public function archive_header() {
        if(is_archive() && have_posts()) {
            $header = sprintf('<div class="caption block">%s</div>',
                $this->archive_title() . $this->archive_description()
            );

            /**
             * Filter archive header block
             *
             * @since 1.3
             * @param string $header
             */
            echo apply_filters('knife_archive_header', $header);
        }
    }


    /*
     * Main template function using instead of default get_template_part
     */
    public function template($slug, $name = null) {
        $templates = [];

        /**
         * Fires before the specified template part file is loaded.
         *
         * @since 1.3
         * @param string $slug
         * @param string|null $name
         */
        do_action('knife_template', $slug, $name);

        // Get custom name according class method
        $method = 'template_' . $slug;

        if($name === null && method_exists(__CLASS__, $method)) {
            $name = $this->$method();
        }

        // If name exists, return template array
        if((string) $name !== '') {
            $templates[] = "templates/{$slug}-{$name}.php";
        }

        // Default template part by slug
        $templates[] = "templates/{$slug}.php";

        return $templates;
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


    /**
     * Common method to output posts info meta
     */
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
     * Get content template part
     */
    private function template_content() {
        $template = get_post_type();

        if(post_type_supports($template, 'post-formats')) {
            $template = get_post_format();
        }

        /**
         * Filters content template
         *
         * @since 1.3
         * @param string $template
         */
        return apply_filters('knife_template_content', $template);
    }


    /**
     * Get content template part
     */
    private function template_archive() {
        $template = null;

        /**
         * Filters archive template
         *
         * @since 1.3
         * @param string $template
         */
        return apply_filters('knife_template_archive', $template);
    }


    /**
     * Custom archive title
     */
    private function archive_title() {
        $title = get_the_archive_title();

        if(is_post_type_archive()) {
            return sprintf('<h1 class="caption__title caption__title--pure">%s</h1>',
                post_type_archive_title('', false)
            );
        }

        if(is_category()) {
            return sprintf('<h1 class="caption__title caption__title--large">%s</h1>',
                single_term_title('', false)
            );
        }

        if(is_tag() || is_tax()) {
            return sprintf('<h1 class="caption__title">%s</h1>',
                single_term_title('', false)
            );
        }

        return sprintf('<h1 class="caption__title">%s</h1>', $title);
    }


    /**
     * Custom archive description
     */
    private function archive_description() {
        $description = get_the_archive_description();

        if(!empty($description)) {
            $description = sprintf('<div class="caption__text">%s</div>', $description);
        }

        return $description;
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
