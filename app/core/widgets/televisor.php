<?php
/**
 * Television widget
 *
 * Widget shows 4 posts and recent news block
 *
 * @package knife-theme
 * @since 1.4
 */


class Knife_Televisor_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'televisor',
            'description' => __('Выводит телевизор из 4 постов и блока новостей', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_widget_televisor', __('[НОЖ] Телевизор', 'knife-theme'), $widget_ops);
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
            'filter' => -1,
            'unique' => 0
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Using exclude global query var to avoid posts duplicate
        $exclude = get_query_var('widget_exclude', []);

        echo $args['before_widget'];

        // Show single and units widget
        echo '<div class="widget-televisor__content">';
        $this->show_single($instance, $exclude);
        $this->show_units($instance, $exclude);
        echo '</div>';

        // Show recent widget in sidebar
        echo '<div class="widget-televisor__sidebar">';
        $this->show_recent($instance);
        echo '</div>';

        echo $args['after_widget'];
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
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;
        $instance['filter'] = absint($new_instance['filter']);
        $instance['link'] = esc_url($new_instance['link']);


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
            'filter' => -1,
            'unique' => 0
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

        // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('unique')),
            esc_attr($this->get_field_name('unique')),
            __('Только уникальные посты', 'knife-theme'),
            checked($instance['unique'], 1, false)
        );

        // Categories filter
        $categories = wp_dropdown_categories([
            'name' => $this->get_field_name('filter'),
            'id' => $this->get_field_id('filter'),
            'show_option_none' => __('Без фильтра', 'knife-theme'),
            'selected' => esc_attr($instance['filter']),
            'class' => 'widefat',
            'echo' => false
        ]);

        printf('<p><label for="%2$s">%1$s</label>%3$s</p>',
            __('Категория записей в сайдбаре:', 'knife-theme'),
            esc_attr($this->get_field_id('filter')),
            $categories
        );

        // Post url
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка на запись во всю ширину:', 'knife-theme'),
            esc_attr($instance['link']),
            __('Абсолютная ссылка с этого сайта', 'knife-theme')
        );

    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'category__not_in' => $filter,
            'posts_per_page' => 3
        ];

        // Check option to show posts only unique posts
        if($unique === 1 && !empty($exclude)) {
            $query['post__not_in'] = $exclude;
        }

        return $query;
    }


    /**
     * Show unit using query loop
     */
    private function show_units($instance, $exclude) {
        $query = new WP_Query($this->get_query($instance, $exclude));

        if($query->have_posts()) :
            echo '<div class="widget-units">';

            while($query->have_posts()) : $query->the_post();
                $head = the_info(
                    '<div class="unit__head meta">', '</div>',
                    ['tag'], false
                );

                $image = sprintf(
                    '<div class="unit__image">%s</div>',
                    get_the_post_thumbnail(null, 'triple', ['class' => 'unit__image-thumbnail'])
                );

                $link = sprintf(
                    '<a class="unit__content-link" href="%1$s">%2$s</a>',
                    esc_html(get_permalink()),
                    get_the_title()
                );

                $meta = the_info(
                    '<div class="unit__content-meta meta">', '</div>',
                    ['author', 'date'], false
                );

                $content = sprintf(
                    '<div class="unit__content">%s</div>',
                    $link . $meta
                );

                printf(
                    '<div class="unit unit--triple"><div class="unit__inner">%s</div></div>',
                    $head . $image . $content
                );
            endwhile;

            echo '</div>';

            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));
            wp_reset_query();
        endif;
    }


    /**
     * Show single post part
     */
    private function show_single($instance, $exclude) {
        $post_id = url_to_postid($instance['link']);

        $query = new WP_Query([
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'post_type' => 'any',
            'ignore_sticky_posts' => 1,
            'post__in' => [$post_id]
        ]);

        if($query->have_posts()) : $query->the_post();
            echo '<div class="widget-single">';

            the_info(
                '<div class="widget-single__head meta">', '</div>',
                ['tag']
            );

            printf(
                '<div class="widget-single__image">%s</div>',
                get_the_post_thumbnail(null, 'single', ['class' => 'widget-single__image-thumbnail'])
            );

            $title = sprintf(
                '<a class="widget-single__content-title" href="%2$s">%1$s</a>',
                get_the_title(),
                esc_url(get_permalink())
            );

            $meta = the_info(
                '<div class="widget-single__content-meta meta">', '</div>',
                ['author', 'date'], false
            );

            printf(
                '<div class="widget-single__content">%s</div>',
                $title . $meta
            );

            echo '</div>';

            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));
            wp_reset_query();
        endif;
    }


    /**
     * Show recent news part
     */
    private function show_recent($instance) {
        $query = new WP_Query([
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'cat' => $instance['filter'],
            'posts_per_page' => 7
        ]);

        if($query->have_posts()) :
            echo '<div class="widget-recent">';

            printf(
                '<a class="widget-recent__head" href="%2$s">%1$s</a>',
                __('Новости', 'knife-theme'),
                esc_url(get_category_link($instance['filter']))
            );

            while($query->have_posts()) : $query->the_post();
                echo '<div class="widget-recent__item">';

                the_info(
                    '<div class="widget-recent__item-meta meta">', '</div>',
                    ['time', 'tag'], true
                );

                printf(
                    '<a class="widget-recent__item-link" href="%1$s">%2$s</a>',
                    get_permalink(),
                    get_the_title()
                );

                echo '</div>';

            endwhile;

            printf(
                '<a class="widget-recent__more button" href="%2$s">%1$s</a>',
                __('Все новости', 'knife-theme'),
                esc_url(get_category_link($instance['filter']))
            );

            echo '</div>';

            wp_reset_query();
        endif;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Televisor_Widget');
});
