<?php
/**
 * Adult content
 *
 * Add checkbox to determine posts with adult content
 *
 * @package knife-theme
 * @since 1.11
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Adult_Content {
    /**
     * Post meta to store adult content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_adult = '_knife-adult-content';

    /**
     * Post meta to store horror content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_horror = '_knife-horror-content';

    /**
     * Default post type with adult content
     *
     * @access  public
     * @var     array
     */
    public static $post_type = array( 'post', 'club', 'quiz' );

    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to mark adult content
        add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'print_checkbox' ), 12 );

        // Update adult posts meta
        add_action( 'save_post', array( __CLASS__, 'save_meta' ) );

        // Include required horror data
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inject_object' ), 12 );
    }

    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox( $post ) {
        if ( ! in_array( $post->post_type, self::$post_type, true ) ) {
            return;
        }

        $adult = get_post_meta( $post->ID, self::$meta_adult, true );

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr( self::$meta_adult ),
            esc_html__( 'Содержимое для взрослых', 'knife-theme' ),
            checked( $adult, 1, false )
        );

        $horror = get_post_meta( $post->ID, self::$meta_horror, true );

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr( self::$meta_horror ),
            esc_html__( 'Поставить заглушку 18+ на запись', 'knife-theme' ),
            checked( $horror, 1, false )
        );
    }

    /**
     * Save post meta
     */
    public static function save_meta( $post_id ) {
        if ( isset( $_POST['_inline_edit'] ) ) {
            $verify = wp_verify_nonce( sanitize_key( $_POST['_inline_edit'] ), 'inlineeditnonce' );

            if ( $verify ) {
                return;
            }
        }

        if ( ! in_array( get_post_type( $post_id ), self::$post_type, true ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        delete_post_meta( $post_id, self::$meta_adult );
        delete_post_meta( $post_id, self::$meta_horror );

        if ( ! empty( $_REQUEST[ self::$meta_adult ] ) ) {
            update_post_meta( $post_id, self::$meta_adult, 1 );
        }

        if ( ! empty( $_REQUEST[ self::$meta_horror ] ) ) {
            update_post_meta( $post_id, self::$meta_horror, 1 );
        }
    }

    /**
     * Include required id loader data
     *
     * @since 1.17
     */
    public static function inject_object() {
        if ( ! is_singular() ) {
            return;
        }

        $post_id = get_queried_object_id();

        // Get horror meta
        $horror = get_post_meta( $post_id, self::$meta_horror, true );

        if ( empty( $horror ) ) {
            return;
        }

        $options = array(
            'button' => __( 'Да, мне 18 или больше!', 'knife-theme' ),
            'labels' => array(
                __( 'Вы сегодня отлично выглядите! Нескромный вопрос, а вам уже точно есть 18 лет?', 'knife-theme' ),
                __( 'Про вас ли поет Сергей Жуков: «забирай меня скорей, увози за сто морей» — вы уже 18+?', 'knife-theme' ),
                __( 'Если вы ребенок, включайте «Смешарики». А наша статья только для тех, кому уже есть 18 лет.  ', 'knife-theme' ),
                __( 'Этот черный вигвам — только для совершеннолетних. Рискните войти и заглянуть в бездну?', 'knife-theme' ),
                __( 'Вы вступаете на территорию взрослого контента, который может повлиять на ваше развитие. Вам есть 18 лет?', 'knife-theme' ),
            ),
        );

        wp_localize_script( 'knife-theme', 'knife_horror_popup', $options );
    }
}

/**
 * Load current module environment
 */
Knife_Adult_Content::load_module();
