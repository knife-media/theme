<?php
/**
 * Common widgets handler
 *
 * Use for cross-widget functions
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Widget_Handler {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_assets' ) );

        // Include all widgets
        add_action( 'after_setup_theme', array( __CLASS__, 'include_widgets' ) );

        // Register widget areas
        add_action( 'widgets_init', array( __CLASS__, 'register_sidebars' ) );

        // Unregister default widgets
        add_action( 'widgets_init', array( __CLASS__, 'unregister_defaults' ), 1 );

        // Hide default widgets title
        add_filter( 'widget_title', '__return_empty_string' );

        // Clear cache on save post
        add_action( 'added_post_meta', array( __CLASS__, 'clear_cache' ) );
        add_action( 'deleted_post_meta', array( __CLASS__, 'clear_cache' ) );
        add_action( 'updated_post_meta', array( __CLASS__, 'clear_cache' ) );
        add_action( 'deleted_post', array( __CLASS__, 'clear_cache' ) );
        add_action( 'save_post', array( __CLASS__, 'clear_cache' ) );

        // Clear cache on widget update
        add_filter( 'widget_update_callback', array( __CLASS__, 'update_widget' ) );

        // Clear cache on widget reorder
        add_action( 'wp_ajax_widgets-order', array( __CLASS__, 'clear_cache' ), 1 );

        // Cache widget output
        add_filter( 'widget_display_callback', array( __CLASS__, 'cache_widget' ), 10, 3 );
    }

    /**
     * Register widget areas
     */
    public static function register_sidebars() {
        register_sidebar(
            array(
                'name'          => esc_html__( 'Главная страница', 'knife-theme' ),
                'id'            => 'knife-frontal',
                'description'   => esc_html__( 'Добавленные виджеты появятся на главной странице.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Зона под меню', 'knife-theme' ),
                'id'            => 'knife-feature',
                'description'   => esc_html__( 'Добавленные виджеты появятся под шапкой на главной и внутренних страницах.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--feature">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Баннер в шапке', 'knife-theme' ),
                'id'            => 'knife-billboard',
                'description'   => esc_html__( 'Добавленные виджеты появятся над главным меню.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--billboard">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Сайдбар', 'knife-theme' ),
                'id'            => 'knife-sidebar',
                'description'   => esc_html__( 'Добавленные виджеты появятся в сайдбаре на странице постов.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--sidebar">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Внутри записи', 'knife-theme' ),
                'id'            => 'knife-inpost',
                'description'   => esc_html__( 'Добавленные виджеты появятся в равномерно внутри поста.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--inpost">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Подвал записи', 'knife-theme' ),
                'id'            => 'knife-bottom',
                'description'   => esc_html__( 'Добавленные виджеты появятся внизу на странице поста.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--bottom">',
                'after_widget'  => '</div>',
            )
        );

        register_sidebar(
            array(
                'name'          => esc_html__( 'Скрипты и счетчики', 'knife-theme' ),
                'id'            => 'knife-flexible',
                'description'   => esc_html__( 'Добавленные виджеты загрузятся в последнюю очередь. Для скриптов и счетчиков.', 'knife-theme' ),
                'before_widget' => '<div class="widget-%2$s widget-%2$s--flexible">',
                'after_widget'  => '</div>',
            )
        );
    }

    /**
     * Remove default widgets to prevent printing unready styles on production
     */
    public static function unregister_defaults() {
        unregister_widget( 'WP_Widget_Pages' );
        unregister_widget( 'WP_Widget_Calendar' );
        unregister_widget( 'WP_Widget_Archives' );
        unregister_widget( 'WP_Widget_Links' );
        unregister_widget( 'WP_Widget_Meta' );
        unregister_widget( 'WP_Widget_Search' );
        unregister_widget( 'WP_Widget_Categories' );
        unregister_widget( 'WP_Widget_Recent_Posts' );
        unregister_widget( 'WP_Widget_Recent_Comments' );
        unregister_widget( 'WP_Widget_RSS' );
        unregister_widget( 'WP_Widget_Media_Video' );
        unregister_widget( 'WP_Widget_Tag_Cloud' );
        unregister_widget( 'WP_Nav_Menu_Widget' );
        unregister_widget( 'WP_Widget_Custom_HTML' );
        unregister_widget( 'WP_Widget_Text' );
        unregister_widget( 'WP_Widget_Audio' );
        unregister_widget( 'WP_Widget_Media_Audio' );
        unregister_widget( 'WP_Widget_Media_Image' );
        unregister_widget( 'WP_Widget_Media_Gallery' );
    }

    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets( $hook ) {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_media();

        if ( $hook === 'widgets.php' ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'underscore' );
        }

        wp_enqueue_script( 'knife-widget-screen', $include . '/scripts/widget-screen.js', array( 'jquery' ), $version, true );

        $options = array(
            'choose' => esc_html__( 'Выберите обложку', 'knife-theme' ),
        );

        wp_localize_script( 'knife-widget-screen', 'knife_widget_screen', $options );
    }

    /**
     * Include widgets classes
     */
    public static function include_widgets() {
        $include = get_template_directory() . '/core/widgets/';

        foreach ( glob( $include . '/*.php' ) as $widget ) {
            include_once $include . basename( $widget );
        }
    }

    /**
     * Remove widgets cache on save or delete post
     *
     * @since 1.11
     */
    public static function clear_cache() {
        $sidebars = get_option( 'sidebars_widgets' );

        foreach ( $sidebars as $sidebar ) {
            if ( ! is_array( $sidebar ) ) {
                continue;
            }

            foreach ( $sidebar as $widget ) {
                delete_transient( $widget );
            }
        }
    }

    /**
     * Update widget callback
     *
     * @since 1.11
     */
    public static function update_widget( $instance ) {
        self::clear_cache();

        return $instance;
    }

    /**
     * Cache widget output
     */
    public static function cache_widget( $instance, $widget, $args ) {
        if ( $instance === false ) {
            return $instance;
        }

        // Skip cache on preview
        if ( $widget->is_preview() ) {
            return $instance;
        }

        // Get cached version
        $cached_widget = get_transient( $widget->id );

        if ( $cached_widget === false ) {
            ob_start();

            $widget->widget( $args, $instance );
            $cached_widget = ob_get_clean();

            set_transient( $widget->id, $cached_widget, 10 * DAY_IN_SECONDS );
        }

        echo $cached_widget; // phpcs:ignore WordPress.Security.EscapeOutput

        return false;
    }
}


/**
 * Load current module environment
 */
Knife_Widget_Handler::load_module();
