<?php
/**
 * Units widget
 *
 * Widget shows multiple units rows
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.7
 */


class Knife_Widget_Units extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = ['post', 'quiz'];


    /**
     * News category id
     */
    private $news_id = null;


    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'units',
            'description' => __('Выводит полосу по заданному критерию в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        $term = get_category_by_slug('news');

        if(isset($term->term_id)) {
            $this->news_id = $term->term_id;
        }

        parent::__construct('knife_widget_units', __('[НОЖ] Карточки', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 5,
            'offset' => 0,
            'unique' => 1
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Using exclude global query var to avoid posts duplicate
        $exclude = get_query_var('widget_exclude', []);

        // Create new WP_Query by instance vars
        $query = new WP_Query($this->get_query($instance, $exclude));

        if($query->have_posts()) {
            echo $args['before_widget'];

            while($query->have_posts()) {
                $query->the_post();
                $size = $this->calc_size($query->current_post, $instance['posts_per_page']);

                include(get_template_directory() . '/templates/widget-units.php');
            }

            wp_reset_query();
            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));

            echo $args['after_widget'];
        }
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);
        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;

        return $instance;
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 5,
            'offset' => 0,
            'unique' => 1
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Не будет отображаться на странице', 'knife-theme')
        );


        // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('unique')),
            esc_attr($this->get_field_name('unique')),
            __('Только уникальные посты', 'knife-theme'),
            checked($instance['unique'], 1, false)
        );


        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" min="2" max="10" value="%4$s"> <small>%5$s</small></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество записей:', 'knife-theme'),
            esc_attr($instance['posts_per_page']),
            __('(от 2 до 10)', 'knife-theme')
        );


        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('offset')),
            esc_attr($this->get_field_name('offset')),
            __('Пропустить записей:', 'knife-theme'),
            esc_attr($instance['offset'])
        );
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'category__not_in' => $this->news_id,
            'posts_per_page' => $posts_per_page,
            'post_type' => $this->post_type,
            'offset' => $offset,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        ];

        // Check option to show posts only unique posts
        if($unique === 1 && !empty($exclude)) {
            $query['post__not_in'] = $exclude;
        }

        return $query;
    }


    /**
     * Calculate current unit size according loop iterate
     *
     * @since 1.4
     */
    private function calc_size($current, $found) {
        if($found % 5 === 0 && $current % 5 > 2) {
            return 'double';
        }

        if($found % 7 === 0 && $current % 5 < 2) {
            return 'double';
        }

        if($found % 8 === 0 && $current % 5 > 2) {
            return 'double';
        }

        if($found < 3 || $found === 4) {
            return 'double';
        }

        return 'triple';
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Units');
});
