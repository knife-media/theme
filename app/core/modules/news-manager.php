<?php
/**
 * News manager
 *
 * Manage news category
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_News_Manager {
    /**
     * Unique slug using for news category url
     *
     * @access  public
     * @var     string
     */
    public static $news_name = 'news';

    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include news archive template
        add_filter( 'archive_template', array( __CLASS__, 'include_archive' ) );

        // Change posts count on news archive
        add_action( 'pre_get_posts', array( __CLASS__, 'update_count' ) );
    }

    /**
     * Include custom archive template for news
     */
    public static function include_archive( $template ) {
        if ( ! is_feed() && is_category( self::$news_name ) ) {
            $new_template = locate_template( array( 'templates/archive-news.php' ) );

            if ( ! empty( $new_template ) ) {
                return $new_template;
            }
        }

        return $template;
    }

    /**
     * Change posts_per_page for news category archive template
     */
    public static function update_count( $query ) {
        if ( $query->is_main_query() && get_query_var( 'category_name' ) === self::$news_name ) {
            $query->set( 'posts_per_page', 20 );
        }
    }
}


/**
 * Load current module environment
 */
Knife_News_Manager::load_module();
