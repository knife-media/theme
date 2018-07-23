<?php
/**
* Common theme filters
*
* Useful template manager class
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

(new Knife_Theme_Filters)->init();

class Knife_Theme_Filters {
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

        // Add sidebar wrapper if exists
        add_action('dynamic_sidebar_before', [$this, 'sidebar_before'], 10, 2);
        add_action('dynamic_sidebar_after', [$this, 'sidebar_after'], 10, 2);
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


    /**
     *
     */
    public function sidebar_before($index, $has_widgets) {
        if($has_widgets === false) {
            return;
        }

        if(is_single() && $index === 'knife-post-widgets') {
            echo '<div class="post__footer-widgets">';
        }
    }


    /**
     *
     */
    public function sidebar_after($index, $has_widgets) {
        if($has_widgets === false) {
            return;
        }

        if(is_single() && $index === 'knife-post-widgets') {
            echo '</div>';
        }
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

        if(is_author()) {
            return sprintf('<h1 class="caption__title caption__title--author">%s</h1>',
                get_the_author()
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

        if(is_author()) {
            $description = get_the_author_meta('description');
        }

        if(!empty($description)) {
            $description = sprintf('<div class="caption__text">%s</div>', $description);
        }

        return $description;
    }
}
