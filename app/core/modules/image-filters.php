<?php
/**
 * Image filters
 *
 * Required admin-side image filters
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Image_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom image sizes
        add_action( 'after_setup_theme', array( __CLASS__, 'add_image_sizes' ) );

        // Remove useless image attributes
        add_action( 'after_setup_theme', array( __CLASS__, 'remove_attributes' ) );

        // Disable post attachment pages
        add_action( 'template_redirect', array( __CLASS__, 'redirect_attachments' ) );

        // Filters the maximum image width to be included in a 'srcset' attribute
        add_filter( 'max_srcset_image_width', array( __CLASS__, 'set_srcset_width' ) );

        // We want to use own image sizes in post
        add_filter( 'image_size_names_choose', array( __CLASS__, 'set_image_names' ) );

        // Remove default useless large and medium sizes
        add_filter( 'intermediate_image_sizes', array( __CLASS__, 'remove_image_sizes' ) );

        // Wrap all images in editor with figure
        add_filter( 'image_send_to_editor', array( __CLASS__, 'update_editor_image' ), 10, 9 );

        // Update max editor image size
        add_filter( 'editor_max_image_size', array( __CLASS__, 'update_max_editor_size' ), 10, 3 );

        // Replace ImageMagic with GD
        add_filter( 'wp_image_editors', array( __CLASS__, 'change_image_editor' ) );
    }

    /**
     * Compress images with GD instead of Imagick
     * Try to fix 504 error on image uploading
     */
    public static function change_image_editor() {
        return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
    }

    /**
     * Update max editor image size
     *
     * @since 1.16
     */
    public static function update_max_editor_size( $max_image_size, $size, $context ) {
        if ( $context === 'edit' ) {
            $max_image_size = 1280;
        }

        return $max_image_size;
    }

    /**
     * Add custom image sizes
     */
    public static function add_image_sizes() {
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 300, 300, true );

        add_image_size( 'outer', 1024, 9999, false );
        add_image_size( 'inner', 640, 9999, false );
        add_image_size( 'highres', 1280, 9999, false );
        add_image_size( 'short', 640, 640, true );

        add_image_size( 'triple', 480, 360, true );
        add_image_size( 'double', 640, 480, true );
        add_image_size( 'single', 1280, 360, true );
    }

    /**
     * Disable post attachment pages
     */
    public static function redirect_attachments() {
        if ( ! is_attachment() ) {
            return;
        }

        global $wp_query;

        $wp_query->set_404();
        status_header( 404 );
    }

    /**
     * Remove useless image attributes
     */
    public static function remove_attributes() {
        add_filter(
            'get_image_tag_class',
            function() {
                $class = 'figure__image';

                return $class;
            },
            0,
            4
        );

        // Remove attachment link at all
        add_filter(
            'attachment_link',
            function() {
                return;
            }
        );

        // Disable WordPress captions to replace them by own
        add_filter(
            'disable_captions',
            function() {
                return true;
            }
        );

        // We don't want to use default gallery styles anymore
        add_filter(
            'use_default_gallery_style',
            function() {
                return false;
            }
        );
    }

    /**
     * Filters the maximum image width to be included in a 'srcset' attribute
     */
    public static function set_srcset_width( $width ) {
        $width = 640;

        return $width;
    }

    /**
     * We want to use own image sizes in post
     */
    public static function set_image_names( $size_names ) {
        global $_wp_additional_image_sizes;

        $size_names = array(
            'outer'     => esc_html__( 'На всю ширину', 'knife-theme' ),
            'inner'     => esc_html__( 'По ширине текста', 'knife-theme' ),
            'highres'   => esc_html__( 'Высокое разрешение', 'knife-theme' ),
            'full'      => esc_html__( 'Исходный размер', 'knife-theme' ),
            'short'     => esc_html__( 'Обрезанный по высоте', 'knife-theme' ),
            'thumbnail' => esc_html__( 'Миниатюра', 'knife-theme' ),
        );

        return $size_names;
    }

    /**
     * Remove default useless large and medium sizes
     */
    public static function remove_image_sizes( $def_sizes ) {
        unset( $def_sizes['medium'] );
        unset( $def_sizes['large'] );

        return $def_sizes;
    }

    /**
     * Wrap all images in editor with figure
     */
    public static function update_editor_image( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
        $html = get_image_tag( $id, $alt, '', $align, $size );

        if ( $url ) {
            $html = '<a href="' . esc_attr( $url ) . '">' . $html . '</a>';
        }

        if ( $caption ) {
            $html = $html . '<figcaption class="figure__caption">' . $caption . '</figcaption>';
        }

        $html = '<figure class="figure figure--' . esc_attr( $size ) . '">' . $html . '</figure>';

        return $html;
    }
}


/**
 * Load current module environment
 */
Knife_Image_Filters::load_module();
