<?php
/**
* Special projects
*
* Custom special projects taxonomy settings
*
* @package knife-theme
* @since 1.3
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Special_Projects {
    /**
     * Unique slug using for taxonomy register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $slug = 'special';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Register taxonomy
        add_action('after_setup_theme', [__CLASS__, 'register_taxonomy']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 15);


        add_filter('single_post_title', function($title, $post) {
            if(in_the_loop()) {
                return '<div class="caption">' . $title . '</div>';
            }

            return $title;
        }, 10, 2);
    }


    /**
     * Create custom taxonomy
     */
    public static function register_taxonomy() {
        register_taxonomy(self::$slug, 'post', [
            'labels' => [
                'name'                       => __('Спецпроекты', 'knife-theme'),
                'singular_name'              => __('Спецпроект', 'knife-theme'),
                'search_items'               => __('Поиск', 'knife-theme'),
                'popular_items'              => __('Популярные спецпроекты', 'knife-theme'),
                'all_items'                  => __('Все', 'knife-theme'),
                'edit_item'                  => __('Редактировать', 'knife-theme'),
                'update_item'                => __('Обновить', 'knife-theme'),
                'add_new_item'               => __('Добавить новый', 'knife-theme'),
                'new_item_name'              => __('Новый спецпроект', 'knife-theme'),
                'separate_items_with_commas' => __('Разделить записи запятыми', 'knife-theme'),
                'add_or_remove_items'        => __('Добавить или удалить тип', 'knife-theme'),
                'choose_from_most_used'      => __('Наиболее используемые', 'knife-theme'),
                'not_found'                  => __('Не найдено', 'knife-theme'),
                'menu_name'                  => __('Спецпроекты', 'knife-theme'),
            ],
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => self::$slug],
        ]);
    }


    /**
     * Update special post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title($title) {
        if(is_tax(self::$slug)) {
            $title = sprintf('<h1 class="tagline-title tagline-title--%2$s">%1$s</h1>',
                single_term_title('', false), esc_attr(self::$slug)
            );
        }

        return $title;
    }

}


/**
 * Load current module environment
 */
Knife_Special_Projects::load_module();
