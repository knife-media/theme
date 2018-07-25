<?php
/**
 * Story widget
 *
 * 4 stories in a row
 *
 * @package knife-theme
 * @since 1.3
 */


class Knife_Story_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'story',
            'description' => __('Выводит полосу из историй в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_story', __('[НОЖ] Истории', 'knife-theme'), $widget_ops);
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
            'posts_per_page' => 4,
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        // Check cache before creating WP_Query object
        $html = get_transient($this->id);

        if($html === false) :
            $q = new WP_Query([
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'post_type' => 'story',
                'offset' => $offset,
                'posts_per_page' => $posts_per_page
            ]);

            ob_start();

            if($q->have_posts() && $q->found_posts >= $posts_per_page) :
                while($q->have_posts()) : $q->the_post();

                    echo $args['before_widget'];

                    $image = sprintf('<div class="widget__image">%s</div>',
                        get_the_post_thumbnail(null, 'triple', ['class' => 'widget__image-thumbnail'])
                    );

                    $link = sprintf('<a class="widget__link" href="%2$s">%1$s</a>',
                        the_title('<p class="widget__title">', '</p>', false),
                        esc_url(get_permalink())
                    );

                    printf('<article class="widget__item">%1$s<footer class="widget__footer">%2$s</footer></article>',
                        $image, $link
                    );

                    echo $args['after_widget'];

                endwhile;
            endif;

            wp_reset_query();

            $html = ob_get_clean();
            set_transient($this->id, $html, 24 * HOUR_IN_SECONDS);
        endif;

        echo $html;
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

        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);
        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['post_type'] = sanitize_text_field($new_instance['post_type']);

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'posts_per_page' => 4,
            'offset' => 0
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

        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество записей:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );


        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('offset')),
            esc_attr($this->get_field_name('offset')),
            __('Пропустить записей:', 'knife-theme'),
            esc_attr($instance['offset'])
        );
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Story_Widget');
});
