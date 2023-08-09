<?php
/**
 * Preview image
 * Add preview box for some posts
 *
 * @package knife-theme
 * @since 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Preview_Image {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add localization strings for preview links
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inject_localization' ) );
    }

    /**
     * Add localization string for preview links
     *
     * @since 1.17
     */
    public static function inject_localization() {
        if ( ! is_singular() ) {
            return;
        }

        $options = array(
            'external' => __( 'Открыть в новом окне', 'knife-theme' ),
            'warning'  => __( 'Не удалось загрузить изображение', 'knife-theme' ),
            'alt'      => __( 'Внешнее изображение', 'knife-theme' ),
        );

        wp_localize_script( 'knife-theme', 'knife_preview_links', $options );
    }
}

/**
 * Load current module environment
 */
Knife_Preview_Image::load_module();
