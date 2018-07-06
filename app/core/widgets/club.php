<?php
/**
 * Club widget
 *
 * 5 posts with create post button
 *
 * @package knife-theme
 * @since 1.3
 */


class Knife_Club_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'club',
            'description' => __('Выводит блок записей в клуб.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_club', __('[НОЖ] Клуб', 'knife-theme'), $widget_ops);
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
            'posts_per_page' => 5,
            'offset' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        $html = get_transient($this->id);

        if($html === false) :

            // Get query by widget args
            $q = new WP_Query([
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'post_type' => 'club',
                'posts_per_page' => $posts_per_page,
                'offset' => $offset
            ]);

            ob_start();

            echo $args['before_widget'];

            printf('<div class="widget__head"><a class="widget__head-link" href="%2$s">%1$s</a></div>',
                esc_html($title),
                get_post_type_archive_link('club')
            );

            while($q->have_posts()) : $q->the_post();
                echo '<div class="widget__item"><footer class="widget__footer">';

                the_info(
                    '<div class="widget__meta meta">', '</div>',
                    ['author', 'date']
                );

                printf(
                    '<a class="widget__link" href="%2$s">%1$s</a>',
                    the_title('<p class="widget__title">', '</p>', false),
                    get_permalink()
                );

                echo '</footer></div>';
            endwhile;

            if(!empty($link)) :
                printf('<div class="widget__item"><a class="widget__button button bright" href="%2$s">%1$s</a></div>',
                    __('Написать в клуб', 'knife-theme'),
                    esc_url($link)
                );
            endif;

            echo $args['after_widget'];

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
        $instance['link'] = sanitize_text_field($new_instance['link']);

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
            'link' => '',
            'posts_per_page' => 5,
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
            __('Отобразится на странице в лейбле', 'knife-theme')
        );

        // Button link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с кнопки:', 'knife-theme'),
            esc_attr($instance['link'])
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
    register_widget('Knife_Club_Widget');
});
