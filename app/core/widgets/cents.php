<?php
/**
 * Cents widget
 *
 * Short posts from selected telegram channel
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.13
 */


class Knife_Widget_Cents extends WP_Widget {
    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'cents',
            'description' => __('Короткие записи из Телеграм-канала', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        if(!defined('KNIFE_CENTS')) {
            define('KNIFE_CENTS', []);
        }

        parent::__construct('knife_widget_cents', __('[НОЖ] Копейки', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts' => [],
            'posts_per_page' => 4,
            'page' => null
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Try to find cents page
        $instance = $this->assign_page($instance);

        // Try to append post cents
        $instance = $this->assign_posts($instance);

        if(!empty($instance['posts']) && !empty($instance['page'])) {
            echo $args['before_widget'];

            // Cents widget template
            include(get_template_directory() . '/templates/widget-cents.php');

            echo $args['after_widget'];
        }
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 4
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Posts per page option
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title'])
        );

        // Posts per page option
         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество записей:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);

        return $instance;
    }


    /**
     * Try to find cents page
     */
    private function assign_page($instance) {
        if(isset(KNIFE_CENTS['page'])) {
            $page = get_page_by_path(KNIFE_CENTS['page']);

            if(isset($page->ID)) {
                $instance['page'] = $page->ID;
            }
        }

        return $instance;
    }


    /**
     * Try to get items from page meta
     */
    private static function assign_posts($instance) {
        if(property_exists('Knife_Cents_Page', 'meta_cents')) {
            $cents = get_post_meta($instance['page'], Knife_Cents_Page::$meta_cents, true);

            if(is_array($cents)) {
                $instance['count'] = count($cents);

                // Cut array for posts_per_page elements
                $instance['posts'] = array_slice($cents, 0, $instance['posts_per_page']);
            }
        }

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Cents');
});
