<?php
/**
* Special projects
*
* Custom special projects taxonomy settings
*
* @package knife-theme
* @since 1.3
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Special_Projects;

class Knife_Special_Projects {
    /**
     * Unique slug using for taxonomy register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'special';


    public function __construct() {
        // Register taxonomy
        add_action('init', [$this, 'register_taxonomy']);

        // Add special single header
        add_action('knife_template', [$this, 'single_header']);

        // Filter special archive header
        add_filter('knife_archive_header', [$this, 'archive_header']);

        // Add custom background to single post if exists
        add_filter('knife_custom_background', [$this, 'update_background'], 10, 2);

        // Add custom background for special taxonomy
        add_filter('knife_custom_background_taxes', function($default) {
            $default[] = $this->slug;

            return $default;
        });
    }


    /**
     * Create custom taxonomy
     */
    public function register_taxonomy() {
        register_taxonomy($this->slug, 'post', [
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
            'rewrite'               => ['slug' => $this->slug],
        ]);
    }


     /**
     * Append header special taxonomy link
     */
    public function single_header() {
        if(is_single() && has_term('', $this->slug)) {
            $terms = wp_get_post_terms(get_queried_object_id(), $this->slug);

            $header = sprintf('<a class="caption special" href="%1$s"><h1>%2$s</h1></a>',
                esc_url(get_term_link($terms[0]->term_id)),
                esc_html($terms[0]->name)
            );

            echo $header;
        }
    }


    /**
     * Filter archive special header
     */
    public function archive_header($header) {
        if(is_tax($this->slug)) {
            $header = sprintf('<div class="caption special"><h1>%s</h1></div>',
                single_term_title('', false)
            );
        }

        return $header;
    }


    /**
     * Set custom background from term meta
     */
    public function update_background($background, $meta) {
        if(is_single() && has_term('', $this->slug)) {
            $post_terms = wp_get_post_terms(get_queried_object_id(), $this->slug);

            // Check only first term
            $background = get_term_meta($post_terms[0]->term_id, $meta, true);
        }

        return $background;
    }
}
