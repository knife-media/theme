<?php
/**
 * Recent widget
 *
 * Recent posts widget showing as narrow column
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.7
 */


class Knife_Widget_Recent extends WP_Widget {
    /**
     * News category id
     */
    private $news_id = null;


    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'recent',
            'description' => __('Выводит последние посты c датой и тегом по выбранной категории.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        $term = get_category_by_slug('news');

        if(isset($term->term_id)) {
            $this->news_id = $term->term_id;
        }

        parent::__construct('knife_widget_recent', __('[НОЖ] Новости', 'knife-theme'), $widget_ops);
    }


   /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 7
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $query = new WP_Query([
            'cat' => $this->news_id,
            'posts_per_page' => $instance['posts_per_page'],
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        ]);

        if($query->have_posts()) {
            echo $args['before_widget'];

            printf(
                '<a class="widget-recent__head head" href="%2$s">%1$s</a>',
                esc_html($instance['title']),
                esc_url(get_category_link($this->news_id))
            );

            while($query->have_posts()) {
                $query->the_post();

                include(get_template_directory() . '/templates/widget-recent.php');
            }

            printf(
                '<a class="widget-recent__more button" href="%2$s">%1$s</a>',
                __('Все новости', 'knife-theme'),
                esc_url(get_category_link($this->news_id))
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
            'posts_per_page' => 7
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Отобразится на странице в лейбле', 'knife-theme')
        );

         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество постов:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

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
