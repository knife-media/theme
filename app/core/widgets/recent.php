<?php
/**
 * Recent widget
 *
 * Recent posts widget showing as narrow column
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.17
 */


class Knife_Widget_Recent extends WP_Widget {
    /**
     * News category slug
     */
    private $news_name = 'news';

    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'recent',
            'description'                 => esc_html__( 'Выводит последние посты c датой и тегом по выбранной категории.', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_recent', esc_html__( '[НОЖ] Новости', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {
        $defaults = array(
            'title'          => '',
            'posts_per_page' => 7,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        $query = new WP_Query(
            array(
                'category_name'       => $this->news_name,
                'posts_per_page'      => $instance['posts_per_page'],
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
            )
        );

        if ( $query->have_posts() ) {
            echo $args['before_widget']; // phpcs:ignore

            // Include recent widget template
            include get_template_directory() . '/templates/widget-recent.php';

            echo $args['after_widget']; // phpcs:ignore
        }
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'          => '',
            'posts_per_page' => 7,
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_html__( 'Заголовок:', 'knife-theme' ),
            esc_attr( $instance['title'] ),
            esc_html__( 'Отобразится на странице в лейбле', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'posts_per_page' ) ),
            esc_attr( $this->get_field_name( 'posts_per_page' ) ),
            esc_html__( 'Количество постов:', 'knife-theme' ),
            esc_attr( $instance['posts_per_page'] )
        );
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field( $new_instance['title'] );

        // int to avoid phpcs warning
        $instance['posts_per_page'] = (int) $new_instance['posts_per_page'];

        return $instance;
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Recent' );
    }
);
