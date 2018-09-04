<?php
class Color_Picker_Widget_25809 extends WP_Widget {

    /**
     * Widget constructor.
     *
     * @since  1.0
     *
     * @access public
     */
    public function __construct() {
        parent::__construct(
            'color-picker',
            _x( 'Color Picker', 'widget title', 'color-picker-widget' ),
            array(
                'classname'   => 'color-picker-widget',
                'description' => _x( 'Widget with a color picker', 'widget description', 'color-picker-widget' )
            )
        );
    }

    /**
     * Widget output.
     *
     * @since  1.0
     *
     * @access public
     * @param  array $args
     * @param  array $instance
     */
    public function widget( $args, $instance ) {
        extract( $args );

        // Title
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : _x( 'Color Picker', 'widget title', 'color-picker-widget' );
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        // Colors
        $color1 = ( ! empty( $instance['color1'] ) ) ? $instance['color1'] : '#fff';
        $color2 = ( ! empty( $instance['color2'] ) ) ? $instance['color2'] : '#f00';

        echo $before_widget;
        if ( $title )
            echo $before_title . $title . $after_title;
        ?>
            <div style="height: 100px; width: 100%; background-color:<?php echo $color1; ?>"></div>
            <div style="height: 100px; width: 100%; background-color:<?php echo $color2; ?>"></div>
        <?php
        echo $after_widget;
    }

    /**
     * Saves widget settings.
     *
     * @since  1.0
     *
     * @access public
     * @param  array $new_instance
     * @param  array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance[ 'title' ]  = strip_tags( $new_instance['title'] );
        $instance[ 'color1' ] = strip_tags( $new_instance['color1'] );
        $instance[ 'color2' ] = strip_tags( $new_instance['color2'] );

        return $instance;
    }

    /**
     * Prints the settings form.
     *
     * @since  1.0
     *
     * @access public
     * @param  array $instance
     */
    public function form( $instance ) {
        // Defaults
        $instance = wp_parse_args(
            $instance,
            array(
                'title' => _x( 'Color Picker', 'widget title', 'color-picker-widget' ),
                'color1' => '',
                'color2' => ''
            )
        );

        $title = esc_attr( $instance[ 'title' ] );
        $color1 = esc_attr( $instance[ 'color1' ] );
        $color2 = esc_attr( $instance[ 'color2' ] );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'color1' ); ?>"><?php _e( 'Color 1:' ); ?></label><br>
            <input type="text" name="<?php echo $this->get_field_name( 'color1' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'color1' ); ?>" value="<?php echo $color1; ?>" data-default-color="#fff" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'color2' ); ?>"><?php _e( 'Color 2:' ); ?></label><br>
            <input type="text" name="<?php echo $this->get_field_name( 'color2' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'color2' ); ?>" value="<?php echo $color2; ?>" data-default-color="#f00" />
        </p>
        <?php
    }
}

add_action( 'widgets_init', function() {
    register_widget( 'Color_Picker_Widget_25809' );
} );
