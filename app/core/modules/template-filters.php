<?php
/**
* Common template filters
*
* Useful template manager class
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_Template_Filters;

class Knife_Template_Filters {
    public function __construct() {
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
}
