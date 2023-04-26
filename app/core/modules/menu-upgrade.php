<?php
/**
 * Menu upgrade
 *
 * Filters to upgrade theme menus
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Menu_Upgrade {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register theme menus
        add_action( 'after_setup_theme', array( __CLASS__, 'register_menus' ) );

        // Change default menu items class
        add_filter( 'nav_menu_css_class', array( __CLASS__, 'update_css_classes' ), 10, 3 );

        // Add class to menu item link
        add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'update_link_attributes' ), 10, 3 );

        // We have to change titles to icons in social menu
        add_filter( 'nav_menu_item_title', array( __CLASS__, 'update_item_title' ), 10, 3 );

        // Remove menu ids
        add_filter( 'nav_menu_item_id', '__return_empty_string' );
    }

    /**
     * Register theme menus
     */
    public static function register_menus() {
        register_nav_menus(
            array(
                'main'   => esc_html__( 'Верхнее меню', 'knife-theme' ),
                'mobile' => esc_html__( 'Дополнительное меню', 'knife-theme' ),
                'pages'  => esc_html__( 'Служебное меню', 'knife-theme' ),
                'social' => esc_html__( 'Меню социальных ссылок', 'knife-theme' ),
            )
        );
    }

    /**
     * Add class to menu item link
     */
    public static function update_link_attributes( $atts, $item, $args ) {
        $atts['class'] = 'menu__item-link';

        if ( $args->theme_location === 'social' ) {
            $atts['class'] = 'social__item-link';
        }

        return $atts;
    }

    /**
     * We have to change titles to icons in social menu
     */
    public static function update_item_title( $title, $item, $args ) {
        if ( $args->theme_location === 'social' ) {
            return sprintf( '<span class="icon icon--%1$s" title="%1$s"></span>', strtolower( $title ) );
        }

        return $title;
    }

    /**
     * Change default menu items classes
     */
    public static function update_css_classes( $classes, $item, $args ) {
        // Redefine classes array
        $classes = array();

        // Set default item class
        $item_class = 'menu__item';

        if ( $args->theme_location === 'social' ) {
            $item_class = 'social__item';
        }

        $classes[] = $item_class;

        foreach ( (array) get_post_meta( $item->ID, '_menu_item_classes', true ) as $class ) {
            if ( ! empty( $class ) ) {
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
