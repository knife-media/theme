<?php
/**
 * Television widget
 *
 * Widget shows 4 posts and recent news block
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.9
 */


class Knife_Widget_Televisor extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = ['post', 'quiz'];


    /**
     * News category id
     */
    private $news_id = null;


    /**
     * Exclude tags
     */
    private $tag__not_in = [];


    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = [
            'classname' => 'televisor',
            'description' => __('Выводит телевизор из 4 постов и блока новостей', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        $cat = get_category_by_slug('news');
        if(isset($cat->term_id)) {
            $this->news_id = $cat->term_id;
        }

        $tag = get_term_by('slug', 'bluntmedia', 'post_tag');
        if(isset($tag->term_id)) {
            $this->tag__not_in[] = (int) $tag->term_id;
        }

        parent::__construct('knife_widget_televisor', __('[НОЖ] Телевизор', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'cover' => 0,
            'unique' => 1,
            'posts_per_page' => 7,
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        echo $args['before_widget'];

        include(get_template_directory() . '/templates/widget-televisor.php');

        echo $args['after_widget'];
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = $new_instance['title'];
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;
        $instance['link'] = esc_url($new_instance['link']);
        $instance['cover'] = absint($new_instance['cover']);

        return $instance;
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'cover' => 0,
            'unique' => 1,
            'posts_per_page' => 7,
            'picture' => ''
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Post url
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с фичера:', 'knife-theme'),
            esc_attr($instance['link']),
            __('На внешний ресурс или запись c этого сайта', 'knife-theme')
        );


        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Заполните, чтобы обновить заголовок фичера', 'knife-theme')
        );


        // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('unique')),
            esc_attr($this->get_field_name('unique')),
            __('Только уникальные посты', 'knife-theme'),
            checked($instance['unique'], 1, false)
        );


        // News count
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr($this->get_field_id('posts_per_page')),
            esc_attr($this->get_field_name('posts_per_page')),
            __('Количество новостей:', 'knife-theme'),
            esc_attr($instance['posts_per_page'])
        );


        // Widget cover
        if($cover = wp_get_attachment_url($instance['cover'])) {
            $instance['picture'] = sprintf('<img src="%s" alt="" style="max-width: 100%%;">', esc_url($cover));
        }

        printf(
            '<p>%5$s<input id="%1$s" name="%2$s" type="hidden" value="%3$s"><button type="button" class="button knife-widget-image">%4$s</button></p>',
            esc_attr($this->get_field_id('cover')),
            esc_attr($this->get_field_name('cover')),
            esc_attr($instance['cover']),
            __('Выбрать обложку фичера', 'knife-theme'),
            $instance['picture']
        );
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'category__not_in' => $this->news_id,
            'posts_per_page' => 3,
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
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
    private function show_units($instance) {
        $exclude = get_query_var('widget_exclude', []);
        $query = new WP_Query($this->get_query($instance, $exclude));

        if($query->have_posts()) {
            echo '<div class="widget-units">';

            while($query->have_posts()) {
                $query->the_post();
                $size = 'triple';

                include(get_template_directory() . '/templates/widget-units.php');
            }

            wp_reset_query();
            set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($query->posts, 'ID')));

            echo '</div>';
        }
    }


    /**
     * Show single post part
     */
    private function show_single($instance) {
        $post_id = url_to_postid($instance['link']);

        $query = new WP_Query([
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'post_type' => 'any',
            'ignore_sticky_posts' => 1,
            'post__in' => [$post_id]
        ]);

        if($query->have_posts()) {
            $exclude = get_query_var('widget_exclude', []);
            set_query_var('widget_exclude', array_merge($exclude, [$post_id]));

            return $this->display_internal($instance, $query);
        }

        return $this->display_external($instance);
    }


    /**
     * Show recent news part
     */
    private function show_recent($instance) {
        $query = new WP_Query([
            'cat' => $this->news_id,
            'tag__not_in' => $this->tag__not_in,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $instance['posts_per_page']
        ]);

        if($query->have_posts()) {
            echo '<div class="widget-recent">';

            printf(
                '<a class="widget-recent__head head" href="%2$s">%1$s</a>',
                __('Новости', 'knife-theme'),
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
            echo '</div>';
        }
    }


    /**
     * Display template for internal single post
     *
     * @since 1.9
     */
    private function display_internal($instance, $query, $internal = true) {
        $query->the_post();

        if(empty($instance['title'])) {
            $instance['title'] = get_the_title();
        }

        $instance['link'] = get_permalink();

        echo '<div class="widget-single">';
        include(get_template_directory() . '/templates/widget-single.php');
        echo '</div>';

        wp_reset_query();
    }


    /**
     * Display template for external single post
     *
     * @since 1.9
     */
    private function display_external($instance, $internal = false) {
        echo '<div class="widget-single">';
        include(get_template_directory() . '/templates/widget-single.php');
        echo '</div>';
    }

}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Televisor');
});
