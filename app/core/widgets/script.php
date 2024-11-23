<?php
/**
 * Script widget
 *
 * Widget custom html code
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */


class Knife_Widget_Script extends WP_Widget {

    /**
     * Default instance.
     */
    protected $default_instance = array(
        'title'   => '',
        'content' => '',
    );

    /**
     * Sets up a new Custom HTML widget instance.
     */
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'script',
            'description'                 => esc_html__( 'Произвольный HTML-код для баннеров и скриптов.', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        $control_ops = array(
            'width'  => 400,
            'height' => 350,
        );

        parent::__construct( 'knife_theme_script', esc_html__( '[НОЖ] HTML-код', 'knife-theme' ), $widget_ops, $control_ops );

        wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.idBases.push( %s );', wp_json_encode( $this->id_base ) ) );

        // Note that the widgets component in the customizer will also do the 'admin_print_scripts-widgets.php' action in WP_Customize_Widgets::print_scripts().
        add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_admin_scripts' ) );

        // Note that the widgets component in the customizer will also do the 'admin_footer-widgets.php' action in WP_Customize_Widgets::print_footer_scripts().
        add_action( 'admin_footer-widgets.php', array( $this, 'render_control_template_scripts' ) );
    }

    /**
     * Outputs the content for the current Custom HTML widget instance.
     */
    public function widget( $args, $instance ) {
        $instance = array_merge( $this->default_instance, $instance );

        // Adds noreferrer and noopener relationships, without duplicating values, to all HTML A elements that have a target.
        $content = $instance['content'];

        echo $args['before_widget'] . $content . $args['after_widget']; // phpcs:ignore
    }

    /**
     * Handles updating settings for the current Custom HTML widget instance.
     */
    public function update( $new_instance, $old_instance ) {
        $instance          = array_merge( $this->default_instance, $old_instance );
        $instance['title'] = sanitize_text_field( $new_instance['title'] );

        if ( current_user_can( 'unfiltered_html' ) ) {
            $instance['content'] = $new_instance['content'];
        } else {
            $instance['content'] = wp_kses_post( $new_instance['content'] );
        }

        return $instance;
    }

    /**
     * Loads the required scripts and styles for the widget control.
     */
    public function enqueue_admin_scripts() {
        $settings = wp_enqueue_code_editor(
            array(
                'type'       => 'text/html',
                'codemirror' => array(
                    'indentUnit' => 2,
                    'tabSize'    => 2,
                ),
            )
        );

        wp_enqueue_script( 'custom-html-widgets' );

        if ( empty( $settings ) ) {
            $settings = array( 'disabled' => true );
        }

        wp_add_inline_script( 'custom-html-widgets', sprintf( 'wp.customHtmlWidgets.init( %s );', wp_json_encode( $settings ) ), 'after' );

        $l10n = array(
            'errorNotice' => array(
                'singular' => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 1 ),
                'plural'   => _n( 'There is %d error which must be fixed before you can save.', 'There are %d errors which must be fixed before you can save.', 2 ),
            ),
        );

        wp_add_inline_script( 'custom-html-widgets', sprintf( 'jQuery.extend( wp.customHtmlWidgets.l10n, %s );', wp_json_encode( $l10n ) ), 'after' );
    }

    /**
     * Outputs the Custom HTML widget settings form.
     */
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->default_instance );

        // Widget title
        printf(
            '<input class="title sync-input" id="%1$s" name="%2$s" type="hidden" value="%3$s">',
            esc_attr( $this->get_field_id( 'title' ) ),
            esc_attr( $this->get_field_name( 'title' ) ),
            esc_attr( $instance['title'] )
        );

        // Widget content
        printf(
            '<textarea class="content sync-input" id="%1$s" name="%2$s" rows="10" hidden>%3$s</textarea>',
            esc_attr( $this->get_field_id( 'content' ) ),
            esc_attr( $this->get_field_name( 'content' ) ),
            esc_textarea( $instance['content'] )
        );
    }

    /**
     * Render form template scripts.
     */
    public function render_control_template_scripts() {
        ?>
        <script type="text/html" id="tmpl-widget-custom-html-control-fields">
            <# var elementIdPrefix = 'el' + String( Math.random() ).replace( /\D/g, '' ) + '_' #>
            <p>
                <label for="{{ elementIdPrefix }}title"><?php esc_html_e( 'Заголовок:', 'knife-theme' ); ?></label>
                <input id="{{ elementIdPrefix }}title" type="text" class="widefat title">
            </p>

            <p>
                <label for="{{ elementIdPrefix }}content" id="{{ elementIdPrefix }}content-label"><?php esc_html_e( 'Содержимое:', 'knife-theme' ); ?></label>
                <textarea id="{{ elementIdPrefix }}content" class="widefat code content" rows="16" cols="20"></textarea>
            </p>

            <div class="code-editor-error-container"></div>
        </script>
        <?php
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Script' );
    }
);
