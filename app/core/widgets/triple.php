<?php
/**
 * Triple widget
 *
 * 3 posts in a row
 *
 * @package knife-theme
 * @since 1.1
 */


class Knife_Triple_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = [
            'classname' => 'triple',
            'description' => __('Выводит полосу из трех постов по заданному критерию в виде карточек.', 'knife-theme'),
            'customize_selective_refresh' => true
        ];

        parent::__construct('knife_theme_triple', __('[НОЖ] Три в ряд', 'knife-theme'), $widget_ops);
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
            'posts_per_page' => 3,
            'offset' => 0,
            'cover' => 'default',
            'unique' => 0,
            'taxonomy' => 'category',
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        extract($instance);

        // Check cache before creating WP_Query object
        $html = get_transient($this->id);

        if($html === false) :
            $exclude = get_query_var('widget_exclude', []);

            $q = new WP_Query($this->get_query($instance, $exclude));

            ob_start();

            if($q->have_posts()) :

                while($q->have_posts()) : $q->the_post();
                    echo $args['before_widget'];

                    $classes = [];

                    $head = the_info(
                        '<div class="widget__head meta">', '</div>',
                        ['tag'], false
                    );

                    $image = sprintf('<div class="widget__image">%s</div>',
                        get_the_post_thumbnail(null, 'triple', ['class' => 'widget__image-thumbnail'])
                    );

                    $link = sprintf('<a class="widget__link" href="%2$s">%1$s</a>',
                        the_title('<p class="widget__title">', '</p>', false),
                        esc_url(get_permalink())
                    );

                    $meta = the_info(
                        '<div class="widget__meta meta">', '</div>',
                        ['author', 'date'], false
                    );

                    $classes[] = 'widget__item';

                    switch($cover) {
                        case 'cover':
                            $classes[] = 'widget__item--cover';

                            break;

                        case 'nocover':
                            break;

                        default:
                            if(!get_post_meta(get_the_ID(), '_knife-cover', true))
                                break;

                            $classes[] = 'widget__item--cover';
                    }

                    printf('<article class="%3$s">%1$s<footer class="widget__footer">%2$s</footer></article>',
                        $head . $image, $link . $meta,
                        implode(' ', $classes)
                    );

                    echo $args['after_widget'];
                endwhile;

                set_query_var('widget_exclude', array_merge($exclude, wp_list_pluck($q->posts, 'ID')));
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

        if(taxonomy_exists($new_instance['taxonomy'])) {
            $taxonomy = $new_instance['taxonomy'];

            if(isset($_REQUEST['widget-id']) && $_REQUEST['widget-id'] == $this->id) {
                $posted_terms = [];

                if(isset($_POST['post_category']))
                    $posted_terms = $_POST['post_category'];

                if(isset($_POST['tax_input'][$taxonomy]))
                    $posted_terms = $_POST['tax_input'][$taxonomy];

                foreach($posted_terms as $term) {
                    if(term_exists(absint($term), $taxonomy))
                        $terms[] = absint($term);
                }
            }
        }

        $instance = $old_instance;

        $instance['posts_per_page'] = absint($new_instance['posts_per_page']);
        $instance['offset'] = absint($new_instance['offset']);
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['cover'] = sanitize_text_field($new_instance['cover']);
        $instance['taxonomy'] = sanitize_text_field($new_instance['taxonomy']);
        $instance['termlist'] = $terms;
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;

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
            'posts_per_page' => 3,
            'offset' => 0,
            'cover' => 'default',
            'unique' => 0,
            'taxonomy' => 'category',
            'termlist' => []
        ];

        $instance = wp_parse_args((array) $instance, $defaults);

        $cover = [
            'defalut' => __('По умолчанию', 'knife-theme'),
            'cover' => __('Использовать подложку', 'knife-theme'),
            'nocover' => __('Убрать подложку', 'knife-theme')
        ];

        $taxes = get_taxonomies([
            'public' => true
        ], 'object');

        $terms = wp_terms_checklist(0, [
            'taxonomy' => $instance['taxonomy'],
            'selected_cats' => $instance['termlist'],
            'echo' => false
        ]);


        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr($this->get_field_id('title')),
            esc_attr($this->get_field_name('title')),
            __('Заголовок:', 'knife-theme'),
            esc_attr($instance['title']),
            __('Не будет отображаться на странице', 'knife-theme')
        );


        // Cover manage
        printf(
            '<p><label for="%1$s">%3$s</label><select class="widefat" id="%1$s" name="%2$s">',
            esc_attr($this->get_field_id('cover')),
             esc_attr($this->get_field_name('cover')),
            __('Подложка карточек:', 'knife-theme')
        );

        foreach($cover as $name => $title) {
            printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($instance['cover'], $name, false));
        }

        echo '</select></p>';


        // Taxonomies filter
        printf(
            '<p><label for="%1$s">%3$s</label><select class="widefat knife-widget-taxonomy" id="%1$s" name="%2$s">',
            esc_attr($this->get_field_id('taxonomy')),
             esc_attr($this->get_field_name('taxonomy')),
            __('Фильтр записей:', 'knife-theme')
        );

        foreach($taxes as $name => $object) {
            printf('<option value="%1$s"%3$s>%2$s</option>', $name, $object->label, selected($instance['taxonomy'], $name, false));
        }

        echo '</select></p>';


        // Terms filter
        printf(
            '<ul class="cat-checklist categorychecklist knife-widget-termlist" id="%1$s">%2$s</ul>',
            esc_attr($this->get_field_id('termlist')),
            $terms
        );


         // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr($this->get_field_id('unique')),
            esc_attr($this->get_field_name('unique')),
            __('Только уникальные посты', 'knife-theme'),
            checked($instance['unique'], 1, false)
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

    /**
     * Generate query params from instance args
     */
    private function get_query($instance, $exclude) {
        extract($instance);

        $query = [
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'offset' => $offset,
            'posts_per_page' => $posts_per_page,
            'tax_query' => [
                [
                    'field' => 'id',
                    'taxonomy' => $taxonomy,
                    'terms' => $termlist
                ],
            ]
        ];

        // Check option to show posts only unique posts
        if($unique === 1 && !empty($exclude)) {
            $query['post__not_in'] = $exclude;
        }

        return $query;
    }
}


/**
 * It is time to register widget
 */
add_action('widgets_init', function() {
    register_widget('Knife_Triple_Widget');
});
