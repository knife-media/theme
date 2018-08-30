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

        // Add special single header
        add_action('knife_template', [__CLASS__, 'single_header']);

        // Filter special archive header
        add_filter('knife_archive_header', [__CLASS__, 'archive_header']);

        // Add custom background to single post if exists
        add_filter('knife_custom_background', [__CLASS__, 'update_background'], 10, 2);
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
     * Append header special taxonomy link
     */
    public static function single_header() {
        if(is_single() && has_term('', self::$slug)) {
            $terms = wp_get_post_terms(get_queried_object_id(), self::$slug);

            printf('<a class="caption special" href="%1$s"><p>%2$s</p></a>',
                esc_url(get_term_link($terms[0]->term_id)),
                esc_html($terms[0]->name)
            );
        }
    }


    /**
     * Filter archive special header
     */
    public static function archive_header($header) {
        if(is_tax(self::$slug)) {
            $header = sprintf('<div class="caption special"><h1>%s</h1></div>',
                single_term_title('', false)
            );
        }

        return $header;
    }


    /**
     * Set custom background from term meta
     */
    public static function update_background($background, $meta) {
        if(is_single() && has_term('', self::$slug)) {
            $post_terms = wp_get_post_terms(get_queried_object_id(), self::$slug);

            // Check only first term
            $background = get_term_meta($post_terms[0]->term_id, $meta, true);
        }

        return $background;
    }
}


/**
 * Load current module environment
 */
Knife_Special_Projects::load_module();
