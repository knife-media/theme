<?php
/**
 * Single widget
 *
 * Full-width widget
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.9
 */


class Knife_Widget_Single extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'single',
            'description' => __('Выводит пост в виде широкого модуля', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_single', __('[НОЖ] На всю ширину', 'knife-theme'), $widget_ops);
    }


     /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'cover' => 0,
            'link' => '',
            'button' => '',
            'picture' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $post_id = url_to_postid($instance['link']);

        $query = new WP_Query([
            'post_status' => 'any',
            'post_type' => 'any',
            'posts_per_page' => 1,
            'ignore_sticky_posts' => 1,
            'post__in' => [$post_id]
        ]);

        // If the required post is found
        if($query->have_posts()) {
            $exclude = get_query_var('widget_exclude', []);
            set_query_var('widget_exclude', array_merge($exclude, [$post_id]));

            return $this->display_internal($instance, $args, $query);
        }

        return $this->display_external($instance, $args);
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['link'] = sanitize_text_field($new_instance['link']);
        $instance['cover'] = absint($new_instance['cover']);
        $instance['button'] = sanitize_text_field($new_instance['button']);

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'cover' => 0,
            'button' => '',
            'picture' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Post url
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка:', 'knife-theme'),
            esc_attr($instance['link']),
            __('На внешний ресурс или запись c этого сайта', 'knife-theme')
        );


        // Widget cover
        if($cover = wp_get_attachment_url($instance['cover'])) {
            $instance['picture'] = sprintf('<img src="%s" alt="" style="max-width: 100%%;">', esc_url($cover));
        }

        printf(
            '<p>%5$s<input id="%1$s" name="%2$s" type="hidden" value="%3$s"><button type="button" class="button knife-widget-image">%4$s</button></p>',
            esc_attr($this->get_field_id('cover')),
            esc_attr($this->get_field_name('cover')),
            esc_attr($instance['cover']),
            __('Выбрать обложку', 'knife-theme'),
            $instance['picture']
        );


        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Заполните, чтобы обновить заголовок записи', 'knife-theme')
        );


        // Button text
        printf(
            '<p><label for="%1$s">%3$s</label><input type="text" class="widefat knife-widget-button" data-title="%5$s" id="%1$s" name="%2$s"value="%4$s"></p>',
            esc_attr($this->get_field_id('button')),
            esc_attr($this->get_field_name('button')),
            __('Надпись на кнопке:', 'knife-theme'),
            esc_attr($instance['button']),
            __('Добавить кнопку', 'knife-theme')
        );
    }


    /**
     * Display template for internal posts
     *
     * @since 1.9
     */
    private function display_internal($instance, $args, $query, $internal = true) {
        $query->the_post();

        if(empty($instance['title'])) {
            $instance['title'] = get_the_title();
        }

        $instance['link'] = get_permalink();

        echo $args['before_widget'];
        include(get_template_directory() . '/templates/widget-single.php');
        echo $args['after_widget'];

        wp_reset_query();
    }


    /**
     * Display template for external single post
     *
     * @since 1.9
     */
    private function display_external($instance, $args, $internal = false) {
        echo $args['before_widget'];
        include(get_template_directory() . '/templates/widget-single.php');
        echo $args['after_widget'];
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Single');
});
