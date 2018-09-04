<?php
/**
 * Script widget
 *
 * Recent posts widget showing as bright links
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */


class Knife_Script_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'script',
            'description' => __('Произвольный HTML-код для баннеров и скриптов.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_script', __('[НОЖ] HTML-код', 'knife-theme'), $widget_ops);
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
            'title'   => '',
            'content' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        echo $args['before_widget'] . $instance['content'] . $args['after_widget'];
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    function form($instance) {
        $defaults = [
            'title'   => '',
            'content' => ''
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

        // Widget content
        printf(
            '<p><label for="%1$s">%3$s</label><textarea class="widefat" id="%1$s" name="%2$s" rows="10">%4$s</textarea></p>',
            esc_attr($this->get_field_id('content')),
            esc_attr($this->get_field_name('content')),
            __('HTML-код:', 'knife-theme'),
            esc_attr($instance['content'])
        );

        // Stick widget
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('sticky')),
            esc_attr($this->get_field_name('sticky')),
            __('Прилепить виджет', 'knife-theme'),
            checked($instance['sticky'], 1, false)
        );

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

        $instance['title'] = sanitize_text_field($new_instance['title']);

        if(current_user_can('unfiltered_html')) {
            $instance['content'] = $new_instance['content'];
        } else {
            $instance['content'] = wp_kses_post($new_instance['content']);
        }

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Script_Widget');
});
