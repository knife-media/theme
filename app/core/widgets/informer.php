<?php
/**
 * Informer widget
 *
 * Informer line with optional sticker and custom colors
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */


class Knife_Widget_Informer extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'informer',
            'description' => __('Выводит информер на всю ширину со стикером', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_informer', __('[НОЖ] Информер', 'knife-theme'), $widget_ops);
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
            'link' => '',
            'sticker' => '',
            'color' => '#000000',
            'background' => '#ffe64e'
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        if(!empty($instance['title']) && !empty($instance['link'])) {
            echo $args['before_widget'];

            $styles = [
                'color: ' . $instance['color'],
                'background-color: ' . $instance['background']
            ];

            if(empty($instance['sticker'])) {
                $post_id = url_to_postid($instance['link']);
                $sticker = get_post_meta($post_id, '_knife-sticker', true);
            }

            include(get_template_directory() . '/templates/widget-informer.php');

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

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = esc_url($new_instance['link']);
        $instance['sticker'] = esc_url($new_instance['sticker']);
        $instance['color'] = sanitize_text_field($new_instance['color']);
        $instance['background'] = sanitize_text_field($new_instance['background']);

        return $instance;
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
            'title' => '',
            'link' => '',
            'sticker' => '',
            'color' => '#000000',
            'background' => '#ffe64e'
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок информера', 'knife-theme'),
            esc_attr($instance['title']),
             __('Отобразится на странице', 'knife-theme')
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с информера', 'knife-theme'),
            esc_attr($instance['link'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('sticker')),
            esc_attr($this->get_field_name('sticker')),
            __('Ссылка на стикер', 'knife-theme'),
            esc_attr($instance['sticker']),
            __('По умолчанию отобразится стикер записи', 'knife-theme')
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="color-picker" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('color')),
            esc_attr($this->get_field_name('color')),
            __('Цвет текста', 'knife-theme'),
            esc_attr($instance['color'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="color-picker" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('background')),
            esc_attr($this->get_field_name('background')),
            __('Цвет фона', 'knife-theme'),
            esc_attr($instance['background'])
        );
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Informer');
});
