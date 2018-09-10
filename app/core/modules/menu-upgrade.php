<?php
/**
* Menu upgrade
*
* Filters to upgrade theme menus
*
* @package knife-theme
* @since 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Menu_Upgrade {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register theme menus
        add_action('after_setup_theme', [__CLASS__, 'register_menus']);

        // Add selector menu metabox
        add_action('admin_head-nav-menus.php', [__CLASS__, 'add_menu_metabox']);

        // Change default menu items class
        add_filter('nav_menu_css_class', [__CLASS__, 'update_css_classes'], 10, 3);

        // Remove menu ids
        add_filter('nav_menu_item_id', '__return_empty_string');

        // Add class to menu item link
        add_filter('nav_menu_link_attributes', [__CLASS__, 'update_link_attributes'], 10, 3);

        // We have to change titles to icons in social menu
        add_filter('nav_menu_item_title', [__CLASS__, 'update_item_title'], 10, 3);

        // Remove separator menu item link
        add_filter('walker_nav_menu_start_el', [__CLASS__, 'remove_separator_link'], 10, 2);
    }


    /**
     * Register theme menus
     */
    public static function register_menus() {
        register_nav_menus([
            'main' => __('Верхнее меню', 'knife-theme'),
            'footer' => __('Нижнее меню', 'knife-theme'),
            'social' => __('Меню социальных ссылок', 'knife-theme')
        ]);
    }


    /**
     * Add class to menu item link
     */
    public static function update_link_attributes($atts, $item, $args) {
        if($args->theme_location === 'main') {
            $atts['class'] = 'menu__item-link';
        }

        if($args->theme_location === 'footer') {
            $atts['class'] = 'footer__nav-link';
        }

        if($args->theme_location === 'social') {
            $atts['class'] = 'social__item-link';
        }

        return $atts;
    }


    /**
     * We have to change titles to icons in social menu
     */
    public static function update_item_title($title, $item, $args) {
        if($args->theme_location === 'social') {
            return sprintf('<span class="icon icon--%1$s" title="%1$s"></span>', strtolower($title));
        }

        return $title;
    }


    /**
     * Change default menu items classes
     */
    public static function update_css_classes($classes, $item, $args) {
        // Redefine classes array
        $classes = [];

        if($args->theme_location === 'main') {
            $classes[] = 'menu__item';
        }

        if($args->theme_location === 'footer') {
            $classes[] = 'footer__nav-item';
        }

        if($args->theme_location === 'social') {
            $classes[] = 'social__item';
        }

        return array_merge($classes, (array) get_post_meta($item->ID, '_menu_item_classes', true));
    }


    /**
     * Remove separator item menu link
     */
    public static function remove_separator_link($item_output, $item) {
        if($item->type === 'separator') {
            $item_output = '';
        }

        return $item_output;
    }

    /**
     * Add separator menu metabox
     */
    public static function add_menu_metabox() {
        add_meta_box('separator-metabox', __('Разделители', 'knife-theme'), [__CLASS__, 'display_menu_metabox'], 'nav-menus', 'side', 'default');
    }


    /**
     * Render separator metabox
     */
    public static function display_menu_metabox() {
        global $nav_menu_selected_id;

        $walker = new Walker_Nav_Menu_Checklist([]);

        $separator = (object) [
            'ID' => 1,
            'db_id' => 0,
            'menu_item_parent' => 0,
            'object_id' => 1,
            'post_parent' => 0,
            'type' => 'separator',
            'object' => 'separator',
            'type_label' => '',
            'title' => __('Мобильный разделитель', 'knife-theme'),
            'url' => '#',
            'target' => '',
            'attr_title' => '',
            'description' => '',
            'classes' => ['menu__item--separator'],
            'xfn' => '',
        ];

        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/menu-metabox.php');
    }
}


/**
 * Load current module environment
 */
Knife_Menu_Upgrade::load_module();
