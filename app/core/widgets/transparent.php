<?php
/**
 * Transparent widget
 *
 * Transparent recent posts with emoji
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.17
 */


class Knife_Widget_Transparent extends WP_Widget {
    /**
     * Widget post types
     */
    private $post_type = array( 'post', 'quiz' );

    /**
     * Categories to show in units
     */
    private $category = array( 'longreads', 'play' );

    /**
     * Repeated emojis array
     */
    private $repeat_emoji = array();


    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'transparent',
            'description'                 => esc_html__( 'Выводит список из четырех прозрачных постов со стикерами.', 'knife-theme' ),
            'customize_selective_refresh' => true,
        );

        parent::__construct( 'knife_widget_transparent', esc_html__( '[НОЖ] Прозрачный', 'knife-theme' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget.
     */
    public function widget( $args, $instance ) {
        $defaults = array(
            'title'    => '',
            'link'     => '',
            'offset'   => 0,
            'termlist' => array(),
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

        // Get query vars
        $query = new WP_Query( $this->get_query( $instance ) );

        if ( $query->have_posts() ) {
            echo $args['before_widget']; // phpcs:ignore

            // Inclute transparent widget template
            include get_template_directory() . '/templates/widget-transparent.php';

            echo $args['after_widget']; // phpcs:ignore
        }
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $taxonomy = 'post_tag';

        if ( isset( $_REQUEST['widget-id'] ) && $_REQUEST['widget-id'] === $this->id ) {
            $posted_terms = array();

            if ( isset( $_POST['post_category'] ) ) {
                $posted_terms = wp_unslash( $_POST['post_category'] ); // phpcs:ignore
            }

            if ( isset( $_POST['tax_input'][ $taxonomy ] ) ) {
                $posted_terms = wp_unslash( $_POST['tax_input'][ $taxonomy ] ); // phpcs:ignore
            }

            foreach ( $posted_terms as $term ) {
                if ( term_exists( absint( $term ), $taxonomy ) ) {
                    $terms[] = absint( $term );
                }
            }
        }

        $instance = $old_instance;

        $instance['offset']   = absint( $new_instance['offset'] );
        $instance['title']    = sanitize_text_field( $new_instance['title'] );
        $instance['link']     = esc_url( $new_instance['link'] );
        $instance['termlist'] = $terms;

        return $instance;
    }

    /**
     * Back-end widget form.
     */
    public function form( $instance ) {
        $defaults = array(
            'title'    => '',
            'link'     => '',
            'offset'   => 0,
            'termlist' => array(),
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

        // Widget title link
        printf(
            '<p><label for="%1$s">%3$s</label><input class="widefat" id="%1$s" name="%2$s" type="text" value="%4$s"><small>%5$s</small></p>',
            esc_attr( $this->get_field_id( 'link' ) ),
            esc_attr( $this->get_field_name( 'link' ) ),
            esc_html__( 'Ссылка с лейбла:', 'knife-theme' ),
            esc_attr( $instance['link'] ),
            esc_html__( 'Можно оставить поле пустым', 'knife-theme' )
        );

        // Posts offset
        printf(
            '<p><label for="%1$s">%3$s</label> <input class="tiny-text" id="%1$s" name="%2$s" type="number" value="%4$s"></p>',
            esc_attr( $this->get_field_id( 'offset' ) ),
            esc_attr( $this->get_field_name( 'offset' ) ),
            esc_html__( 'Пропустить записей:', 'knife-theme' ),
            esc_attr( $instance['offset'] )
        );

        $checklist = wp_terms_checklist(
            0,
            array(
                'taxonomy'      => 'post_tag',
                'selected_cats' => $instance['termlist'],
                'echo'          => false,
            )
        );

        // Terms filter
        printf(
            '<ul class="cat-checklist categorychecklist knife-widget-termlist" id="%1$s">%2$s</ul>',
            esc_attr( $this->get_field_id( 'termlist' ) ),
            preg_replace( '~id="[^"]+"~', '', $checklist ) // phpcs:ignore
        );
    }

    /**
     * Get tag emoji
     *
     * @since 1.11
     */
    private function get_emoji( $post_id, $emoji = '' ) {
        $terms = get_the_tags( $post_id );

        if ( property_exists( 'Knife_Terms_Emoji', 'term_meta' ) ) {
            $term_meta = Knife_Terms_Emoji::$term_meta;

            foreach ( wp_list_pluck( $terms, 'term_id' ) as $term_id ) {
                $emoji = get_term_meta( $term_id, $term_meta, true );

                if ( ! in_array( $term_id, $this->repeat_emoji, true ) ) {
                    array_push( $this->repeat_emoji, $term_id );

                    break;
                }
            }
        }

        return $emoji;
    }

    /**
     * Generate query params from instance args
     */
    private function get_query( $instance ) {
        extract( $instance ); // phpcs:ignore

        $query = array(
            'offset'              => $offset,
            'posts_per_page'      => 4,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'tax_query'           => array( // phpcs:ignore
                'relation' => 'AND',
                array(
                    'field'    => 'id',
                    'taxonomy' => 'post_tag',
                    'terms'    => $termlist,
                ),
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $this->category,
                ),
            ),
        );

        return $query;
    }
}


/**
 * It is time to register widget
 */
add_action(
    'widgets_init',
    function() {
        register_widget( 'Knife_Widget_Transparent' );
    }
);
