<?php
/**
* Common widgets handler
*
* Use for cross-widget functions
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


(new Knife_Widget_Handler)->init();

class Knife_Widget_Handler {
   /**
    * Unique nonce for widget ajax requests
    *
    * @access   private
    * @var      string
    */
    private $nonce = 'knife-widget-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public function init() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // include all widgets
        add_action('after_setup_theme', [$this, 'include_widgets']);

        // register widget areas
        add_action('widgets_init', [$this, 'register_sidebars']);

        // unregister default widgets
        add_action('widget_init', [$this, 'unregister_defaults'], 11);

        // hide default widgets title
        add_filter('widget_title', '__return_empty_string');

        // clear cache
        add_action('added_post_meta', [$this, 'clear_cache']);
        add_action('deleted_post_meta', [$this, 'clear_cache']);
        add_action('updated_post_meta', [$this, 'clear_cache']);
        add_action('deleted_post', [$this, 'clear_cache']);
        add_action('save_post', [$this, 'clear_cache']);
        add_action('widget_update_callback', [$this, 'clear_cache']);

        add_action('wp_ajax_knife_widget_terms', [$this, 'ajax_terms']);
    }


    /**
     * Register widget areas
     */
    public function register_sidebars() {
        register_sidebar([
            'name'          => __('Главная страница', 'knife-theme'),
            'id'            => 'knife-frontal',
            'description'   => __('Добавленные виджеты появятся на главной странице под телевизором, если он не пуст.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'   => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Телевизор на главной', 'knife-theme'),
            'id'            => 'knife-feature-stripe',
            'description'   => __('Добавленные виджеты появятся в телевизоре на главной странице.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s widget--split">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'    => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Сквозной над шапкой', 'knife-theme'),
            'id'            => 'knife-over-header',
            'description'   => __('Добавленные виджеты появятся над меню на всех страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s widget--header">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'    => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Сквозной под шапкой', 'knife-theme'),
            'id'            => 'knife-under-header',
            'description'   => __('Добавленные виджеты появятся под шапкой на главной и внутренних страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s widget--header">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'    => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Подвал сайта', 'knife-theme'),
            'id'            => 'knife-footer',
            'description'   => __('Добавленные виджеты появятся справа в футере.', 'knife-theme'),
            'before_widget' => '<aside class="widget widget-text">',
            'after_widget'  => '</aside>',
            'before_title'  => '<p class="widget__title">',
            'after_title'   => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Сайдбар на главной', 'knife-theme'),
            'id'            => 'knife-feature-sidebar',
            'description'   => __('Добавленные виджеты появятся справа от телевизора.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s widget--split">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'   => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Сайдбар на внутренних', 'knife-theme'),
            'id'            => 'knife-inner-sidebar',
            'description'   => __('Добавленные виджеты появятся в сайдбаре внутри постов.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s widget--sidebar">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'   => '</p>'
        ]);

        register_sidebar([
            'name'          => __('Виджеты под записью', 'knife-theme'),
            'id'            => 'knife-post-widgets',
            'description'   => __('Добавленные виджеты появятся в под записью на внутренних страницах.', 'knife-theme'),
            'before_widget' => '<div class="widget widget-%2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="widget__title">',
            'after_title'   => '</p>'
        ]);
    }


    /**
     * Remove default widgets to prevent printing unready styles on production
     */
    public function unregister_defaults() {
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
    }


      /**
     * Enqueue assets to admin post screen only
     */
    public function add_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_script('knife-widget-handler', $include . '/scripts/widget-handler.js', ['jquery'], $version);

        $options = [
            'nonce' => wp_create_nonce($this->nonce)
        ];

        wp_localize_script('knife-widget-handler', 'knife_widget_handler', $options);
    }

    /**
     * Include widgets classes
     */
    public function include_widgets() {
        $widgets = get_template_directory() . '/core/widgets/';

        foreach(['club', 'script', 'recent', 'triple', 'double', 'single', 'feature', 'details', 'transparent'] as $id) {
            include_once($widgets . $id . '.php');
        }
    }

    /**
     * Remove widgets cache on save or delete post
     */
    public function clear_cache($instance) {
        $sidebars = get_option('sidebars_widgets');

        foreach($sidebars as $sidebar) {
            if(!is_array($sidebar))
                continue;

            foreach($sidebar as $widget)
                delete_transient($widget);
        }

        return $instance;
    }


    /**
     * Custom terms form by taxonomy name
     */
    public function ajax_terms() {
        check_ajax_referer($this->nonce, 'nonce');

        wp_terms_checklist(0, [
            'taxonomy' => esc_attr($_POST['filter'])
        ]);

        wp_die();
    }
}
