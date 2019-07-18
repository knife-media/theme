<?php
/**
 * Label posts
 *
 * Custom labels posts taxonomy settings
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Label_Posts {
    /**
     * Unique slug using for taxonomy register and url
     *
     * @since   1.5
     * @access  private
     * @var     string
     */
    private static $taxonomy = 'label';


    /**
     * Default post type with posts labels
     *
     * @since   1.8
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'quiz'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register taxonomy
        add_action('init', [__CLASS__, 'register_taxonomy']);
    }


    /**
     * Create custom taxonomy
     */
    public static function register_taxonomy() {
        register_taxonomy(self::$taxonomy, self::$post_type, [
            'labels' => [
                'name'                       => __('Ярлыки', 'knife-theme'),
                'singular_name'              => __('Ярлык', 'knife-theme'),
                'search_items'               => __('Поиск', 'knife-theme'),
                'popular_items'              => __('Популярные ярлыки', 'knife-theme'),
                'all_items'                  => __('Все', 'knife-theme'),
                'edit_item'                  => __('Редактировать', 'knife-theme'),
                'update_item'                => __('Обновить', 'knife-theme'),
                'add_new_item'               => __('Добавить новый', 'knife-theme'),
                'new_item_name'              => __('Новый ярылк', 'knife-theme'),
                'separate_items_with_commas' => __('Разделить записи запятыми', 'knife-theme'),
                'add_or_remove_items'        => __('Добавить или удалить ярлык', 'knife-theme'),
                'choose_from_most_used'      => __('Наиболее используемые', 'knife-theme'),
                'not_found'                  => __('Не найдено', 'knife-theme'),
                'menu_name'                  => __('Ярлыки', 'knife-theme'),
            ],
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => self::$taxonomy],
            'meta_box_cb'           => [__CLASS__, 'print_metabox']
        ]);
    }


    /**
     * Add custom taxonomy metabox without tabs and add-new feature
     */
    public static function print_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/label-metabox.php');
    }
}


/**
 * Load current module environment
 */
Knife_Label_Posts::load_module();
