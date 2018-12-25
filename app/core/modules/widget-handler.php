<?php
/**
* Common widgets handler
*
* Use for cross-widget functions
*
* @package knife-theme
* @since 1.2
* @version 1.7
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Widget_Handler {
   /**
    * Unique nonce for widget ajax requests
    *
    * @access   private
    * @var      string
    */
    private static $nonce = 'knife-widget-nonce';


    /**
    * Ajax action
    *
    * @access  private static
    * @var     string
    * @since   1.7
    */
    private static $action = 'knife-widget-terms';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // include all widgets
        add_action('after_setup_theme', [__CLASS__, 'include_widgets']);

        // register widget areas
        add_action('widgets_init', [__CLASS__, 'register_sidebars']);

        // unregister default widgets
        add_action('widgets_init', [__CLASS__, 'unregister_defaults'], 1);

        // hide default widgets title
        add_filter('widget_title', '__return_empty_string');

        // ajax terms handler
        add_action('wp_ajax_' . self::$action, [__CLASS__, 'ajax_terms']);
    }


    /**
     * Register widget areas
     */
    public static function register_sidebars() {
        register_sidebar([
            'name'          => __('Главная страница', 'knife-theme'),
            'id'            => 'knife-frontal',
            'description'   => __('Добавленные виджеты появятся на главной странице.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s">',
            'after_widget'  => '</div>'
        ]);

        register_sidebar([
            'name'          => __('Фичер под меню', 'knife-theme'),
            'id'            => 'knife-feature',
            'description'   => __('Добавленные виджеты появятся под шапкой на главной и внутренних страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s widget-%2$s--feature">',
            'after_widget'  => '</div>'
        ]);

        register_sidebar([
            'name'          => __('Баннер в шапке', 'knife-theme'),
            'id'            => 'knife-poster',
            'description'   => __('Добавленные виджеты появятся над главным меню.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s widget-%2$s--poster">',
            'after_widget'  => '</div>'
        ]);


        register_sidebar([
            'name'          => __('Сайдбар', 'knife-theme'),
            'id'            => 'knife-sidebar',
            'description'   => __('Добавленные виджеты появятся в сайдбаре внутри постов.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s widget-%2$s--sidebar">',
            'after_widget'  => '</div>'
        ]);
    }


    /**
     * Remove default widgets to prevent printing unready styles on production
     */
    public static function unregister_defaults() {
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Archives');
        unregister_widget('WP_Widget_Links');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Categories');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Media_Video');
        unregister_widget('WP_Widget_Tag_Cloud');
        unregister_widget('WP_Nav_Menu_Widget');
        unregister_widget('WP_Widget_Custom_HTML');
        unregister_widget('WP_Widget_Text');
        unregister_widget('WP_Widget_Audio');
        unregister_widget('WP_Widget_Media_Audio');
        unregister_widget('WP_Widget_Media_Image');
        unregister_widget('WP_Widget_Media_Gallery');
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_media();

        if('widgets.php' === $hook) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('underscore');
        }

        wp_enqueue_script('knife-widget-screen', $include . '/scripts/widget-screen.js', ['jquery'], $version);

        $options = [
            'action' => esc_attr(self::$action),
            'nonce' => wp_create_nonce(self::$nonce),
            'choose' => __('Выберите обложку', 'knife-theme')
        ];

        wp_localize_script('knife-widget-screen', 'knife_widget_screen', $options);
    }


    /**
     * Include widgets classes
     */
    public static function include_widgets() {
        $include = get_template_directory() . '/core/widgets/';

        foreach(glob($include . "/*.php") as $widget) {
            include_once($include . basename($widget));
        }
    }


    /**
     * Custom terms form by taxonomy name
     */
    public static function ajax_terms() {
        check_ajax_referer(self::$nonce, 'nonce');

        wp_terms_checklist(0, [
            'taxonomy' => esc_attr($_POST['filter'])
        ]);

        wp_die();
    }
}


/**
 * Load current module environment
 */
Knife_Widget_Handler::load_module();
