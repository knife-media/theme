<?php
/**
 * Units widget
 *
 * Widget shows multiple units rows
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */


class Knife_Units_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'units',
            'description' => __('Выводит полосу по заданному критерию в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_units', __('[НОЖ] Карточки', 'knife-theme'), $widget_ops);
    }


     /**
     * Outputs the content of the widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array args  The array of form elements
     * @param array instance The current instance of the widget
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 5,
            'offset' => 0,
            'unique' => 0
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
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);
        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;
        $instance['filter'] = absint($new_instance['filter']);

        return $instance;
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 5,
            'offset' => 0,
            'filter' => -1,
            'unique' => 0
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

        // Categories filter
        $categories = wp_dropdown_categories([
            'name' => $this->get_field_name('filter'),
            'id' => $this->get_field_id('filter'),
            'show_option_none' => __('Без фильтра', 'knife-theme'),
            'selected' => esc_attr($instance['filter']),
            'class' => 'widefat',
            'echo' => false
        ]);

        printf('<p><label for="%2$s">%1$s</label>%3$s</p>',
            __('Исключить записи из категории:', 'knife-theme'),
            esc_attr($this->get_field_id('filter')),
            $categories
        );


        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" min="2" max="10" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Число записей от 2 до 10:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );


        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('offset')),
            esc_attr($this->get_field_name('offset')),
            __('Пропустить число постов:', 'knife-theme'),
            esc_attr($instance['offset'])
        );
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'offset' => $offset,
            'category__not_in' => $filter,
            'posts_per_page' => $posts_per_page,
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
    register_widget('Knife_Units_Widget');
});
