<?php
/**
 * Random post
 *
 * Redirect to random post on custom url
 *
 * @package knife-theme
 * @since 1.16
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Random_Post {
    /**
     * Archive query var
     *
     * @access  public
     * @var     string
     */
    public static $query_var = 'random';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Create custom random url
        add_action( 'init', array( __CLASS__, 'add_random_rule' ) );

        // Add query tag
        add_action( 'query_vars', array( __CLASS__, 'append_random_var' ) );

        // Redirect url to random post here
        add_filter( 'template_redirect', array( __CLASS__, 'redirect_random' ), 8 );
    }

    /**
     * Create custom best archive url
     */
    public static function add_random_rule() {
        add_rewrite_rule(
            sprintf( '^%s/?$', self::$query_var ),
            sprintf( 'index.php?%s=1', self::$query_var ),
            'top'
        );
    }

    /**
     * Append best query tag to availible query vars
     */
    public static function append_random_var( $query_vars ) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }

    /**
     * Redirect url to random post here
     */
    public static function redirect_random() {
        if ( ! get_query_var( self::$query_var ) ) {
            return;
        }

        $posts = get_posts(
            array(
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'orderby'        => 'rand',
                'tax_query'      => array( // phpcs:ignore
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => 'longreads',
                    ),
                ),
                'fields'         => 'ids',
            )
        );

        if ( empty( $posts ) ) {
            return;
        }

        $id = self::filter_posts( $posts );

        wp_safe_redirect( get_permalink( $id ), 302 );
        exit;
    }

    /**
     * Filter posts and return first match
     */
    private static function filter_posts( $posts ) {
        if ( ! property_exists( 'Knife_Momentary_Posts', 'meta_momentary' ) ) {
            return $posts[0];
        }

        if ( ! property_exists( 'Knife_Promo_Manager', 'meta_promo' ) ) {
            return $posts[0];
        }

        $id = $posts[0];

        foreach ( $posts as $post ) {
            if ( get_post_meta( $post, Knife_Momentary_Posts::$meta_momentary, true ) ) {
                continue;
            }

            if ( get_post_meta( $post, Knife_Promo_Manager::$meta_promo, true ) ) {
                continue;
            }

            $id = $post;
            break;
        }

        return $id;
    }
}


/**
 * Load current module environment
 */
Knife_Random_Post::load_module();
