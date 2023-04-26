<?php
/**
 * Informer widget
 *
 * Informer line with optional remark and custom colors
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.17
 */


class Knife_Widget_Informer extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'informer',
            'description'                 => esc_html__( 'Выводит информер на всю ширину со стикером', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_informer', esc_html__( '[НОЖ] Информер', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {
        $defaults = array(
            'title'  => '',
            'link'   => '',
            'remark' => '',
            'color'  => '#000000',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        if ( ! empty( $instance['title'] ) && ! empty( $instance['link'] ) ) {
            $post_id = url_to_postid( $instance['link'] );
            $options = $this->get_attributes( $instance, $post_id );

            if ( $post_id === 0 || ! is_single( $post_id ) ) {
                echo $args['before_widget']; // phpcs:ignore

                include get_template_directory() . '/templates/widget-informer.php';

                echo $args['after_widget']; // phpcs:ignore
            }
        }
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['link']   = esc_url( $new_instance['link'] );
        $instance['color']  = sanitize_hex_color( $new_instance['color'] );
        $instance['remark'] = sanitize_text_field( $new_instance['remark'] );

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'  => '',
            'link'   => '',
            'remark' => '',
            'color'  => '#000000',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_html__( 'Заголовок информера:', 'knife-theme' ),
            esc_attr( $instance['title'] ),
            esc_html__( 'Отобразится на странице', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'link' ) ),
            esc_attr( $this->get_field_name( 'link' ) ),
            esc_html__( 'Ссылка с информера:', 'knife-theme' ),
            esc_attr( $instance['link'] ),
            esc_html__( 'Обязательно для заполнения', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'remark' ) ),
            esc_attr( $this->get_field_name( 'remark' ) ),
            esc_html__( 'Пометка:', 'knife-theme' ),
            esc_attr( $instance['remark'] )
        );

        printf(
            '<p><label for="%1$s">%3$s</label><br><input class="color-picker" id="%1$s" name="%2$s" type="text" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'color' ) ),
            esc_attr( $this->get_field_name( 'color' ) ),
            esc_html__( 'Цвет фона:', 'knife-theme' ),
            esc_attr( $instance['color'] )
        );
    }

    /**
     * Generate link attributes
     */
    private function get_attributes( $instance, $post_id, $attributes = array() ) {
        $options = array(
            'href'   => esc_url( $instance['link'] ),
            'target' => '_blank',
            'rel'    => 'noopener',
        );

        $options['style'] = implode(
            '; ',
            array(
                'background-color:' . $instance['color'],
                'color:' . $this->get_text_color( $instance['color'] ),
            )
        );

        foreach ( $options as $key => $value ) {
            $attributes[] = $key . '="' . esc_attr( $value ) . '"';
        }

        return $attributes;
    }

    /**
     * Get text color using relative luminance
     *
     * @link https://en.wikipedia.org/wiki/Relative_luminance
     */
    private function get_text_color( $color ) {
        $color = trim( $color, '#' );

        if ( strlen( $color ) === 3 ) {
            $r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
            $g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
            $b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
        } elseif ( strlen( $color ) === 6 ) {
            $r = hexdec( substr( $color, 0, 2 ) );
            $g = hexdec( substr( $color, 2, 2 ) );
            $b = hexdec( substr( $color, 4, 2 ) );
        }

        // Get relative luminance
        $y = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        return $y > 128 ? '#000' : '#fff';
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Informer' );
    }
);
