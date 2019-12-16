<?php
/**
 * Story widget
 *
 * 4 stories in a row
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */


class Knife_Widget_Story extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = ['story'];


    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'story',
            'description' => __('Выводит полосу из историй в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_story', __('[НОЖ] Истории', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 4,
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $query = new WP_Query([
            'post_type' => $this->post_type,
            'posts_per_page' => $instance['posts_per_page'],
            'offset' => $instance['offset'],
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        ]);


        if($query->have_posts()) {
            echo $args['before_widget'];

            // Include widget story template
            include(get_template_directory() . '/templates/widget-story.php');

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

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 4,
            'offset' => 0
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

        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество записей:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
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
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Story');
});
