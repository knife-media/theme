<?php
/**
 * Feature widget
 *
 * Recent posts widget showing as bright links
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Feature_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'feature',
            'description' => __('Выводит фичер на всю ширину со стикером', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_feature', __('[НОЖ] Фичер', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
        $defaults = ['title' => '', 'link' => '', 'sticker' => ''];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        // Don't show empty link
        if(empty($title) || empty($link))
            return;

        $post_id = url_to_postid($link);

        // Don't show self link inside single post
        if(is_single() && get_queried_object_id() === $post_id)
            return;


        echo $args['before_widget'];

        // right icon
        $widget_right = '<span class="icon icon--right"></span>';

        // set additional widget class to link
        $widget_class = 'widget__link';

        // set default widget image
        $widget_image = '';

        if($post_id > 0) {
            $widget_class =  $widget_class . ' widget__link--banner';
        }

        // get sticker from widget options or post meta
        if(strlen($sticker) < 1) {
            $sticker = get_post_meta($post_id, '_knife-sticker', true);
        }

        // append widget_sticker if image exists

        if(!empty($sticker)) {
            $widget_image = sprintf('<img class="widget__sticker" src="%1$s" alt="%2$s">',
                esc_url($sticker), esc_attr($title)
            );
        }

        // set widget title
        $widget_title = sprintf('<p class="widget__title">%s</p>',
            sanitize_text_field($title)
        );

        // set widget item
        $widget_item = sprintf('<div class="widget__item block">%s</div>',
            $widget_title . $widget_image . $widget_right
        );

        // and finally print widget link
        printf('<a class="%2$s" href="%1$s">%3$s</a>', esc_url($link),
            $widget_class, $widget_item
        );

        echo $args['after_widget'];
  }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
        $defaults = ['title' => '', 'link' => '', 'sticker' => ''];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок фичера', 'knife-theme'),
            esc_attr($instance['title']),
             __('Отобразится на странице', 'knife-theme')
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с фичера', 'knife-theme'),
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
    }


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = esc_url($new_instance['link']);
        $instance['sticker'] = esc_url($new_instance['sticker']);

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Feature_Widget');
});
