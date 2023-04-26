<?php
/**
 * Adfox widget
 *
 * Show adfox banner from direct URL-query
 *
 * @package knife-theme
 * @since 1.11
 * @version 1.17
 */


class Knife_Widget_Adfox extends WP_Widget {
    /**
     * Widget constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'adfox',
            'description'                 => esc_html__( 'Выводит баннер по прямому URL-запросу', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_adfox', esc_html__( '[НОЖ] Баннер AdFox', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {
        $defaults = array(
            'title'    => '',
            'adaptive' => 0,
            'common'   => '',
            'desktop'  => '',
            'mobile'   => '',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        if ( empty( $instance['adaptive'] ) ) {
            return $this->show_common( $instance, $args );
        }

        return $this->show_adaptive( $instance, $args );
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'    => '',
            'adaptive' => 0,
            'common'   => '',
            'desktop'  => '',
            'mobile'   => '',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_html__( 'Заголовок:', 'knife-theme' ),
            esc_attr( $instance['title'] ),
            esc_html__( 'Не отобразится на странице', 'knife-theme' )
        );

        printf(
            '<p><input type="checkbox" id="%1$s" name="%2$s" class="knife-widget-adaptive checkbox"%4$s><label for="%1$s">%3$s</label></p>',
            esc_attr( $this->get_field_id( 'adaptive' ) ),
            esc_attr( $this->get_field_name( 'adaptive' ) ),
            esc_html__( 'Адаптивный код вставки', 'knife-theme' ),
            checked( $instance['adaptive'], 1, false )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'common' ) ),
            esc_attr( $this->get_field_name( 'common' ) ),
            esc_html__( 'Прямой URL-запроса', 'knife-theme' ),
            esc_attr( $instance['common'] ),
            esc_html__( 'Отобразится на любом разрешении', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'desktop' ) ),
            esc_attr( $this->get_field_name( 'desktop' ) ),
            esc_html__( 'Десктопный URL-запроса', 'knife-theme' ),
            esc_attr( $instance['desktop'] )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'mobile' ) ),
            esc_attr( $this->get_field_name( 'mobile' ) ),
            esc_html__( 'Мобильный URL-запроса', 'knife-theme' ),
            esc_attr( $instance['mobile'] )
        );
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']    = sanitize_text_field( $new_instance['title'] );
        $instance['adaptive'] = $new_instance['adaptive'] ? 1 : 0;
        $instance['common']   = sanitize_text_field( $new_instance['common'] );
        $instance['desktop']  = sanitize_text_field( $new_instance['desktop'] );
        $instance['mobile']   = sanitize_text_field( $new_instance['mobile'] );

        return $instance;
    }

    /**
     * Show common widget
     */
    private function show_common( $instance, $args ) {
        if ( ! empty( $instance['common'] ) ) {
            echo $args['before_widget']; // phpcs:ignore

            printf(
                '<div data-common="%s"></div>',
                esc_url( $instance['common'] )
            );

            echo $args['after_widget']; // phpcs:ignore
        }
    }

    /**
     * Show adaptive widget
     */
    private function show_adaptive( $instance, $args ) {
        if ( ! empty( $instance['desktop'] ) || ! empty( $instance['mobile'] ) ) {
            echo $args['before_widget']; // phpcs:ignore

            printf(
                '<div data-desktop="%s" data-mobile="%s"></div>',
                esc_url( $instance['desktop'] ),
                esc_url( $instance['mobile'] )
            );

            echo $args['after_widget']; // phpcs:ignore
        }
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Adfox' );
    }
);
