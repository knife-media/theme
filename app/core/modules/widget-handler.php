<?php
/**
* Common widgets handler
*
* Use for cross-widget functions
*
* @package knife-theme
* @since 1.2
* @version 1.4
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
        add_action('widget_init', [__CLASS__, 'unregister_defaults'], 11);

        // hide default widgets title
        add_filter('widget_title', '__return_empty_string');

        // ajax terms handler
        add_action('wp_ajax_knife_widget_terms', [__CLASS__, 'ajax_terms']);
    }


    /**
     * Register widget areas
     */
    public static function register_sidebars() {
        register_sidebar([
            'name'          => __('Главная страница', 'knife-theme'),
            'id'            => 'knife-frontal',
            'description'   => __('Добавленные виджеты появятся на главной странице под телевизором, если он не пуст.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s">',
            'after_widget'  => '</div>'
        ]);

        register_sidebar([
            'name'          => __('Фичер под меню', 'knife-theme'),
            'id'            => 'knife-feature',
            'description'   => __('Добавленные виджеты появятся под шапкой на главной и внутренних страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s">',
            'after_widget'  => '</div>'
        ]);

        register_sidebar([
            'name'          => __('Подвал сайта', 'knife-theme'),
            'id'            => 'knife-footer',
            'description'   => __('Добавленные виджеты появятся справа в футере.', 'knife-theme'),
            'before_widget' => '<aside class="widget widget-text">',
            'after_widget'  => '</aside>'
        ]);

        register_sidebar([
            'name'          => __('Сайдбар на внутренних', 'knife-theme'),
            'id'            => 'knife-sidebar',
            'description'   => __('Добавленные виджеты появятся в сайдбаре внутри постов.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s widget--sidebar">',
            'after_widget'  => '</div>'
        ]);

        register_sidebar([
            'name'          => __('Виджеты под записью', 'knife-theme'),
            'id'            => 'knife-inside',
            'description'   => __('Добавленные виджеты появятся в под записью на внутренних страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget-%2$s">',
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
    }


    /**
     * Show sidebar if exists
     */
    public static function get_sidebar($id, $sidebar = '') {
        if(is_active_sidebar($id)) {
            ob_start();

            dynamic_sidebar($id);

            $sidebar = ob_get_clean();
        }

        return $sidebar;
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        if('widgets.php' === $hook) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('underscore');
        }

        wp_enqueue_script('knife-widget-handler', $include . '/scripts/widget-handler.js', ['jquery'], $version);

        $options = [
            'nonce' => wp_create_nonce(self::$nonce)
        ];

        wp_localize_script('knife-widget-handler', 'knife_widget_handler', $options);
    }


    /**
     * Include widgets classes
     */
    public static function include_widgets() {
        $include = get_template_directory() . '/core/widgets/';

        $widgets = [
            'story', 'club', 'script', 'televisor', 'recent', 'units', 'single', 'feature', 'details', 'transparent'
        ];

        foreach($widgets as $id) {
            include_once($include . $id . '.php');
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
