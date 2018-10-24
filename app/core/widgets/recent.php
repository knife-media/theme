<?php
/**
 * Recent widget
 *
 * Recent posts widget showing as narrow column
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */


class Knife_Widget_Recent extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'recent',
            'description' => __('Выводит последние посты c датой и тегом по выбранной категории.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_recent', __('[НОЖ] Новости', 'knife-theme'), $widget_ops);
    }


   /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 7,
            'filter' => 620
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $query = new WP_Query([
            'cat' => $instance['filter'],
            'posts_per_page' => $instance['posts_per_page'],
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        ]);

        if($query->have_posts()) {
            echo $args['before_widget'];

            printf(
                '<a class="widget-recent__head head" href="%2$s">%1$s</a>',
                __('Новости', 'knife-theme'),
                esc_url(get_category_link($instance['filter']))
            );

            while($query->have_posts()) {
                $query->the_post();

                include(get_template_directory() . '/templates/widget-recent.php');
            }

            printf(
                '<a class="widget-recent__more button" href="%2$s">%1$s</a>',
                __('Все новости', 'knife-theme'),
                esc_url(get_category_link($instance['filter']))
            );

            wp_reset_query();
            echo $args['after_widget'];
        }
    }


   /**
     * Back-end widget form.
     */
    function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 7,
            'filter' => 620
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $category = wp_dropdown_categories([
            'id' => esc_attr($this->get_field_id('filter')),
            'name' => esc_attr($this->get_field_name('filter')),
            'selected' => esc_attr($instance['filter']),
            'class' => 'widefat',
            'echo' => false,
        ]);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title'])
        );

         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество постов:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );

        printf(
            '<p><label for="%1$s">%2$s</label>%3$s</p>',
            esc_attr($this->get_field_id('filter')),
            __('Рубрика новостей:', 'knife-theme'),
            $category
        );
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['filter'] = (int) $new_instance['filter'];
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
        $instance['title'] = sanitize_text_field($new_instance['title']);

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Recent');
});
