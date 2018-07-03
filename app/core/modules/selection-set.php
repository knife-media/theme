<?php
/**
* Selection set
*
* Custom post type for manual articles collection
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_Selection_Set;

class Knife_Selection_Set {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'select';


    public function __construct() {
        // register club post type
        add_action('init', [$this, 'register_selection']);

        // add post lead to post type editor
        add_filter('knife_post_lead_type', function($default) {
            $default[] = $this->slug;

            return $default;
        });
    }


    /**
     * Register selection post type
     */
    public function register_selection() {
        register_post_type($this->slug, [
            'labels'                => [
                'name'              => __('Подборка', 'knife-theme'),
                'singular_name'     => __('Запись в подборку', 'knife-theme'),
                'add_new'           => __('Добавить запись', 'knife-theme'),
                'menu_name'         => __('Подборки', 'knife-theme')
            ],
            'label'                 => __('Подборка', 'knife-theme'),
            'description'           => __('Подборки статей', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-images-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'taxonomies'            => ['post_tag']
        ]);
    }
}
