<?php
/**
 * Units widget
 *
 * Widget shows multiple units rows
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */


class Knife_Widget_Units extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = array( 'post', 'quiz' );

    /**
     * Categories to show in units
     */
    private $category = array( 'longreads', 'play' );

    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'units',
            'description'                 => esc_html__( 'Выводит полосу по заданному критерию в виде карточек.', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_units', esc_html__( '[НОЖ] Карточки', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {
        $defaults = array(
            'title'          => '',
            'posts_per_page' => 5,
            'offset'         => 0,
            'unique'         => 1,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        // Using exclude global query var to avoid posts duplicate
        $exclude = get_query_var( 'widget_exclude', array() );

        // Create new WP_Query by instance vars
        $query = new WP_Query( $this->get_query( $instance, $exclude ) );

        if ( $query->have_posts() ) {
            echo $args['before_widget']; // phpcs:ignore

            while ( $query->have_posts() ) {
                $query->the_post();
                $size = $this->calc_size( $query->current_post, $instance['posts_per_page'] );

                include get_template_directory() . '/templates/widget-units.php';
            }

            wp_reset_postdata();
            set_query_var( 'widget_exclude', array_merge( $exclude, wp_list_pluck( $query->posts, 'ID' ) ) );

            echo $args['after_widget']; // phpcs:ignore
        }
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['offset'] = absint( $new_instance['offset'] );
        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['unique'] = $new_instance['unique'] ? 1 : 0;

        // Use int to avoid phpcs error
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'          => '',
            'posts_per_page' => 5,
            'offset'         => 0,
            'unique'         => 1,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_html__( 'Заголовок:', 'knife-theme' ),
            esc_attr( $instance['title'] ),
            esc_html__( 'Не будет отображаться на странице', 'knife-theme' )
        );

        // Exclude duplicate
        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr( $this->get_field_id( 'unique' ) ),
            esc_attr( $this->get_field_name( 'unique' ) ),
            esc_html__( 'Только уникальные посты', 'knife-theme' ),
            checked( $instance['unique'], 1, false )
        );

        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" min="2" max="10" value="%4$s"> <small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'posts_per_page' ) ),
            esc_attr( $this->get_field_name( 'posts_per_page' ) ),
            esc_html__( 'Количество записей:', 'knife-theme' ),
            esc_attr( $instance['posts_per_page'] ),
            esc_html__( '(от 2 до 10)', 'knife-theme' )
        );

        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'offset' ) ),
            esc_attr( $this->get_field_name( 'offset' ) ),
            esc_html__( 'Пропустить записей:', 'knife-theme' ),
            esc_attr( $instance['offset'] )
        );
    }

    /**
     * Generate query params from instance args
     */
    private function get_query( $instance, $exclude ) {
        extract( $instance ); // phpcs:ignore

        $query = array(
            'posts_per_page'      => $posts_per_page,
            'post_type'           => $this->post_type,
            'offset'              => $offset,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'tax_query'           => array( // phpcs:ignore
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $this->category,
                ),
            ),
        );

        // Check option to show posts only unique posts
        if ( $unique === 1 && ! empty( $exclude ) ) {
            $query['post__not_in'] = $exclude;
        }

        return $query;
    }

    /**
     * Calculate current unit size according loop iterate
     *
     * @since 1.4
     */
    private function calc_size( $current, $found ) {
        if ( $found % 5 === 0 && $current % 5 > 2 ) {
            return 'double';
        }

        if ( $found % 7 === 0 && $current % 5 < 2 ) {
            return 'double';
        }

        if ( $found % 8 === 0 && $current % 5 > 2 ) {
            return 'double';
        }

        if ( $found < 3 || $found === 4 ) {
            return 'double';
        }

        return 'triple';
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Units' );
    }
);
