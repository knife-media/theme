<?php
/**
 * Menu upgrade
 *
 * Filters to upgrade theme menus
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.7
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

        // Change default menu items class
        add_filter('nav_menu_css_class', [__CLASS__, 'update_css_classes'], 10, 3);

        // Add class to menu item link
        add_filter('nav_menu_link_attributes', [__CLASS__, 'update_link_attributes'], 10, 3);

        // We have to change titles to icons in social menu
        add_filter('nav_menu_item_title', [__CLASS__, 'update_item_title'], 10, 3);

        // Remove menu ids
        add_filter('nav_menu_item_id', '__return_empty_string');
    }


    /**
     * Register theme menus
     */
    public static function register_menus() {
        register_nav_menus([
            'main' => __('Верхнее меню', 'knife-theme'),
            'mobile' => __('Дополнительное меню', 'knife-theme'),
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

        if($args->theme_location === 'mobile') {
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

        if($args->theme_location === 'mobile') {
            $classes[] = 'menu__item';
        }

        if($args->theme_location === 'footer') {
            $classes[] = 'footer__nav-item';
        }

        if($args->theme_location === 'social') {
            $classes[] = 'social__item';
        }

        foreach((array) get_post_meta($item->ID, '_menu_item_classes', true) as $class) {
            if(!empty($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}


/**
 * Load current module environment
 */
Knife_Menu_Upgrade::load_module();
