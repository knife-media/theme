<?php
/**
 * Banner widget
 *
 * Widget for single image banners
 *
 * @package knife-theme
 * @since 1.5
 */


class Knife_Widget_Banner extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'banner',
            'description' => __('Виджет для баннеров.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_banner', __('[НОЖ] Баннер', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title'   => '',
            'image' => '',
            'link' => '',
            'background' => '',
            'styles' => ''
        ];

        $settings = [];
        $instance = wp_parse_args((array) $instance, $defaults);

        if(strlen($instance['background']) > 0) {
            $settings[] = 'background-color: ' . $instance['background'];
        }

        echo $args['before_widget'];

        printf('<a href="%1$s" target="_blank" style="%2$s">%3$s</a>',
            esc_url($instance['link']),
            implode(" ", $settings),

            sprintf('<img src="%1$s" alt="%2$s" style="%3$s">',
                esc_url($instance['image']),
                esc_attr($instance['title']),
                esc_attr($instance['styles'])
            )
        );

        echo $args['after_widget'];
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title'   => '',
            'image' => '',
            'link' => '',
            'background' => '',
            'styles' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Не будет отображаться на странице', 'knife-theme')
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s">%5$s</p>',
            esc_attr($this->get_field_id('image')),
            esc_attr($this->get_field_name('image')),
            __('Ссылка на изображение:', 'knife-theme'),
            esc_attr($instance['image']),
            sprintf(
                '<small><a href="%s" target="_blank">%s</a></small>',
                esc_url(admin_url('/media-new.php')),
                __('Загрузить изображение', 'knife-theme')
            )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с баннера', 'knife-theme'),
            esc_attr($instance['link'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('styles')),
            esc_attr($this->get_field_name('styles')),
            __('Стили изображения', 'knife-theme'),
            esc_attr($instance['styles'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="color-picker" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('background')),
            esc_attr($this->get_field_name('background')),
            __('Цвет фона', 'knife-theme'),
            esc_attr($instance['background'])
        );
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = esc_url($new_instance['link']);
        $instance['image'] = esc_url($new_instance['image']);
        $instance['styles'] = sanitize_text_field($new_instance['styles']);
        $instance['background'] = sanitize_hex_color($new_instance['background']);

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Banner');
});
