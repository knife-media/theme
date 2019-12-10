<?php
/**
 * Adfox widget
 *
 * Show adfox banner from direct URL-query
 *
 * @package knife-theme
 * @since 1.11
 */


class Knife_Widget_Adfox extends WP_Widget {
    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'adfox',
            'description' => __('Выводит баннер по прямому URL-запросу', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_adfox', __('[НОЖ] Баннер AdFox', 'knife-theme'), $widget_ops);
    }


   /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'adaptive' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        if(!empty($instance['link'])) {
            echo $args['before_widget'];

            printf(
                '<div data-link="%s" data-adaptive="%d"></div>',
                esc_url($instance['link']),
                absint($instance['adaptive'])
            );

            echo $args['after_widget'];
        }
    }


   /**
     * Back-end widget form.
     */
    function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'adaptive' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Не отобразится на странице', 'knife-theme')
        );

         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Прямой URL-запроса', 'knife-theme'),
            esc_attr($instance['link'])
        );

        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('adaptive')),
            esc_attr($this->get_field_name('adaptive')),
            __('Адаптивный код вставки', 'knife-theme'),
            checked($instance['adaptive'], 1, false)
        );
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = sanitize_text_field($new_instance['link']);
        $instance['adaptive'] = $new_instance['adaptive'] ? 1 : 0;

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Adfox');
});
