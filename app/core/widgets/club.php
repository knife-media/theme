<?php
/**
 * Club widget
 *
 * 5 posts with create post button
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */


class Knife_Widget_Club extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = array( 'club' );

    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'club',
            'description'                 => esc_html__( 'Выводит блок записей в клуб.', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_club', esc_html__( '[НОЖ] Клуб', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {

        $defaults = array(
            'title'          => '',
            'link'           => '',
            'posts_per_page' => 5,
            'offset'         => 0,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        $query = new WP_Query(
            array(
                'post_type'           => $this->post_type,
                'posts_per_page'      => $instance['posts_per_page'],
                'offset'              => $instance['offset'],
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
            )
        );

        if ( $query->have_posts() ) {
            echo $args['before_widget']; // phpcs:ignore

            // Widget club template
            include get_template_directory() . '/templates/widget-club.php';

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
        $instance['link']   = sanitize_text_field( $new_instance['link'] );

        // int to avoid phpcs warning
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'          => '',
            'link'           => '',
            'posts_per_page' => 5,
            'offset'         => 0,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        // Widget title
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_html__( 'Заголовок:', 'knife-theme' ),
            esc_attr( $instance['title'] ),
            esc_html__( 'Отобразится на странице в лейбле', 'knife-theme' )
        );

        // Button link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'link' ) ),
            esc_attr( $this->get_field_name( 'link' ) ),
            esc_html__( 'Ссылка с кнопки:', 'knife-theme' ),
            esc_attr( $instance['link'] )
        );

        // Posts count
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'posts_per_page' ) ),
            esc_attr( $this->get_field_name( 'posts_per_page' ) ),
            esc_html__( 'Количество записей:', 'knife-theme' ),
            esc_attr( $instance['posts_per_page'] )
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
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Club' );
    }
);
