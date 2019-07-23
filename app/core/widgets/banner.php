<?php
/**
 * Banner widget
 *
 * Widget for single image banners
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.9
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
            'campaign' => '',
            'background' => '',
            'styles' => '',
            'visibility' => 'default'
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        if(!empty($instance['image']) && !empty($instance['link'])) {
            echo $args['before_widget'];

            printf('<a class="%1$s" %2$s>%3$s</a>',
                $this->get_classes($instance, 'widget-banner__link'),
                $this->get_attributes($instance),

                sprintf('<img src="%1$s" alt="%2$s" style="%3$s">',
                    esc_url($instance['image']),
                    esc_attr($instance['title']),
                    esc_attr($instance['styles'])
                )
            );

            echo $args['after_widget'];
        }
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title'   => '',
            'image' => '',
            'link' => '',
            'campaign' => '',
            'background' => '',
            'styles' => '',
            'visibility' => 'default'
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Может отобразиться в плашке баннера', 'knife-theme')
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
            __('Ссылка с баннера:', 'knife-theme'),
            esc_attr($instance['link'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">%4$s</select></p>',
            esc_attr($this->get_field_id('visibility')),
            esc_attr($this->get_field_name('visibility')),
            __('Видимость баннера:', 'knife-theme'),
            $this->show_visibility_options($instance['visibility'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('styles')),
            esc_attr($this->get_field_name('styles')),
            __('Стили изображения:', 'knife-theme'),
            esc_attr($instance['styles'])
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('campaign')),
            esc_attr($this->get_field_name('campaign')),
            __('Название кампании:', 'knife-theme'),
            esc_attr($instance['campaign']),
            __('Событиe в Google Analytics на латинице', 'knife-theme')
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="color-picker" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('background')),
            esc_attr($this->get_field_name('background')),
            __('Цвет фона:', 'knife-theme'),
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
        $instance['campaign'] = sanitize_text_field($new_instance['campaign']);
        $instance['styles'] = sanitize_text_field($new_instance['styles']);
        $instance['background'] = sanitize_hex_color($new_instance['background']);
        $instance['visibility'] = sanitize_text_field($new_instance['visibility']);

        return $instance;
    }


    /**
     * Generate link attributes
     */
    private function get_attributes($instance, $attributes = []) {
        $options = [
            'href' => esc_url($instance['link']),
            'target' => '_blank'
        ];

        if(strlen($instance['title']) > 0) {
            $options['data-title'] = esc_attr(
                $instance['title']
            );
        }

        if(strlen($instance['campaign']) > 0) {
            $options['data-banner'] = esc_attr(
                $instance['campaign']
            );
        }

        if(strlen($instance['background']) > 0) {
            $options['style'] = esc_attr(
                'background-color: ' . $instance['background']
            );
        }

        foreach($options as $key => $value) {
            $attributes[] = $key . '="' . esc_attr($value) . '"';
        }

        return implode(' ', $attributes);
    }


    /**
     * Generate link classes using default value
     *
     * @since 1.7
     */
    private function get_classes($instance, $default) {
        $classes[] = $default;

        if($instance['visibility'] !== 'default') {
            $classes[] = "{$default}--{$instance['visibility']}";
        }

        return implode(' ', $classes);
    }



    /**
     * Show visibility options in visibility select setting
     *
     * @since 1.7
     */
    private function show_visibility_options($selected, $options = '') {
        $labels = [
            'default' => __('Отображать везде', 'knife-theme'),
            'mobile' => __('Только мобильный', 'knife-theme'),
            'desktop' => __('Только десктоп', 'knife-theme')
        ];

        foreach($labels as $name => $title) {
            $options = $options . sprintf(
                '<option value="%1$s"%3$s>%2$s</option>', $name, $title,
                selected($selected, $name, false)
            );
        }

        return $options;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Banner');
});
