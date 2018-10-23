<?php
/**
 * Club widget
 *
 * 5 posts with create post button
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */


class Knife_Widget_Club extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'club',
            'description' => __('Выводит блок записей в клуб.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_club', __('[НОЖ] Клуб', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {

        $defaults = [
            'title' => '',
            'link' => '',
            'posts_per_page' => 5,
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $query = new WP_Query([
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'post_type' => 'club',
            'posts_per_page' => $instance['posts_per_page'],
            'offset' => $instance['offset']
        ]);

        if($query->have_posts()) {
            echo $args['before_widget'];

            printf('<a class="widget-club__head head" href="%2$s">%1$s</a>',
                esc_html($instance['title']),
                esc_url(get_post_type_archive_link('club'))
            );

            while($query->have_posts()) {
                $query->the_post();

                include(get_template_directory() . '/templates/widget-club.php');
            }

            if(!empty($instance['link'])) {
                printf('<div class="widget-club__more"><a class="widget-club__more-button button" href="%2$s">%1$s</a></div>',
                    __('Написать в клуб', 'knife-theme'),
                    esc_url($instance['link'])
                );
            }

            wp_reset_query();
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
        $instance['link'] = sanitize_text_field($new_instance['link']);

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'posts_per_page' => 5,
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
            __('Отобразится на странице в лейбле', 'knife-theme')
        );

        // Button link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с кнопки:', 'knife-theme'),
            esc_attr($instance['link'])
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
    register_widget('Knife_Widget_Club');
});
