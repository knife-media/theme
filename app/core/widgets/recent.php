<?php
/**
 * Recent widget
 *
 * Recent posts widget showing as narrow column
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Recent_Widget extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname' => 'recent',
            'description' => __('Выводит последние посты c датой и тегом по выбранной категории.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_recent', __('[НОЖ] Новости', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 10,
            'cat' => 620
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        // Check cache before creating WP_Query object
        $html = get_transient($this->id);

        if($html === false) :
            $q = new WP_Query([
                'cat' => $cat,
                'posts_per_page' => $posts_per_page,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
            ]);

            ob_start();

            if($q->have_posts()) :
                echo $args['before_widget'];

                while($q->have_posts()) : $q->the_post();
                    echo '<article class="widget__item">';

                    the_info(
                        '<div class="widget__meta meta">', '</div>',
                        ['time', 'tag'], true
                    );

                    printf(
                        '<a class="widget__link" href="%1$s">%2$s</a>',
                        get_permalink(),
                        get_the_title()
                    );

                    echo '</article>';
                endwhile;

                printf(
                    '<a class="widget__more button" href="%2$s">%1$s</a>',
                    __('Все новости', 'knife-theme'),
                    esc_url(get_category_link($cat))
                );

                echo $args['after_widget'];
            endif;

            wp_reset_query();

            $html = ob_get_clean();
            set_transient($this->id, $html, 24 * HOUR_IN_SECONDS);
        endif;

        echo $html;
    }


    /**
     * Outputs the options form on admin
     */
    function form($instance) {
        $defaults = ['title' => '', 'posts_per_page' => 10, 'cat' => 620];
        $instance = wp_parse_args((array) $instance, $defaults);

        $category = wp_dropdown_categories([
            'id' => esc_attr($this->get_field_id('cat')),
            'name' => esc_attr($this->get_field_name('cat')),
            'selected' => esc_attr($instance['cat']),
            'class' => 'widefat',
            'echo' => false,
        ]);

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title'])
        );

         printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество постов:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );

        printf(
            '<p><label for="%1$s">%2$s</label>%3$s</p>',
            esc_attr($this->get_field_id('cat')),
            __('Рубрика записей:', 'knife-theme'),
            $category
        );
    }


    /**
     * Processing widget options on save
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['cat'] = (int) $new_instance['cat'];
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
        $instance['title'] = sanitize_text_field($new_instance['title']);

        $this->remove_cache();

        return $instance;
    }


    /**
     * Remove transient on widget update
     */
     private function remove_cache() {
        delete_transient($this->id);
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Recent_Widget');
});
