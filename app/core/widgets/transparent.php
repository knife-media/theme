<?php
/**
 * Transparent widget
 *
 * Transparent recent posts with emoji
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.11
 */


class Knife_Widget_Transparent extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = ['post', 'quiz', 'generator'];


    /**
     * News category id
     */
    private $news_id = null;


    /**
     * Repeated emojis array
     */
    private $repeat_emoji = [];


    public function __construct() {
        $widget_ops = [
            'classname' => 'transparent',
            'description' => __('Выводит список из четырех прозрачных постов со стикерами.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        $term = get_category_by_slug('news');

        if(isset($term->term_id)) {
            $this->news_id = $term->term_id;
        }

        parent::__construct('knife_widget_transparent', __('[НОЖ] Прозрачный', 'knife-theme'), $widget_ops);
    }


    /**
     * Outputs the content of the widget.
     */
    public function widget($args, $instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'offset' => 0,
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        // Get query vars
        $query = new WP_Query($this->get_query($instance));

        if($query->have_posts()) {
            echo $args['before_widget'];

            // Inclute transparent widget template
            include(get_template_directory() . '/templates/widget-transparent.php');

            echo $args['after_widget'];
        }
    }


    /**
     * Sanitize widget form values as they are saved.
     */
    public function update($new_instance, $old_instance) {
        $taxonomy = 'post_tag';

        if(isset($_REQUEST['widget-id']) && $_REQUEST['widget-id'] == $this->id) {
            $posted_terms = [];

            if(isset($_POST['post_category'])) {
                $posted_terms = $_POST['post_category'];
            }

            if(isset($_POST['tax_input'][$taxonomy])) {
                $posted_terms = $_POST['tax_input'][$taxonomy];
            }

            foreach($posted_terms as $term) {
                if(term_exists(absint($term), $taxonomy)) {
                    $terms[] = absint($term);
                }
            }
        }

        $instance = $old_instance;

        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['link'] = esc_url($new_instance['link']);
        $instance['termlist'] = $terms;

        return $instance;
    }


    /**
     * Back-end widget form.
     */
    public function form($instance) {
        $defaults = [
            'title' => '',
            'link' => '',
            'offset' => 0,
            'termlist' => []
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


        // Widget title link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('link')),
            esc_attr($this->get_field_name('link')),
            __('Ссылка с лейбла:', 'knife-theme'),
            esc_attr($instance['link']),
            __('Можно оставить поле пустым', 'knife-theme')
        );


        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr($this->get_field_id('offset')),
            esc_attr($this->get_field_name('offset')),
            __('Пропустить записей:', 'knife-theme'),
            esc_attr($instance['offset'])
        );


        $checklist = wp_terms_checklist(0, [
            'taxonomy' => 'post_tag',
            'selected_cats' => $instance['termlist'],
            'echo' => false
        ]);


        // Terms filter
        printf(
            '<ul class="cat-checklist categorychecklist knife-widget-termlist" id="%1$s">%2$s</ul>',
            esc_attr($this->get_field_id('termlist')),
            preg_replace('~id="[^"]+"~', '', $checklist)
        );
    }


    /**
     * Get tag emoji
     *
     * @since 1.11
     */
    private function get_emoji($post_id, $emoji = '') {
        $terms = get_the_tags($post_id);

        foreach(wp_list_pluck($terms, 'term_id') as $term_id) {
            $emoji = get_term_meta($term_id, '_knife-term-emoji', true);

            if(!in_array($term_id, $this->repeat_emoji)) {
                array_push($this->repeat_emoji, $term_id);

                break;
            }
        }

        return $emoji;
    }


    /**
     * Generate query params from instance args
     */
    private function get_query($instance) {
        extract($instance);

        $query = [
            'offset' => $offset,
            'category__not_in' => $this->news_id,
            'posts_per_page' => 4,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'tax_query' => [[
                'field' => 'id',
                'taxonomy' => 'post_tag',
                'terms' => $termlist
            ]]
        ];

        return $query;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Widget_Transparent');
});
