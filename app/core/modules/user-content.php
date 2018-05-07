<?php
/**
* User content
*
* Custom ugs blogs post type
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_User_Content;

class Knife_User_Content {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'blog';

    public function __construct() {
        // register blog post type
        add_action('init', [$this, 'register_blog']);
    }


    /**
     * Register story post type
     */
    public function register_blog() {
        register_post_type($this->slug, [
            'labels'                => [
                'all_items'         => __('Все записи', 'knife-theme'),
            ],
            'label'                 => __('Блоги', 'knife-theme'),
            'description'           => __('Пользовательские блоги', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'editor', 'excerpt'],
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 10,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        ]);
    }
}
