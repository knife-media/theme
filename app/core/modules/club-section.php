<?php
/**
 * User content
 *
 * Custom ugs club post type
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Club_Section {

    /**
     * Unique slug using for custom post type register and url
     *
     * @access  public
     * @var     string
     */
    public static $post_type = 'club';

    /**
     * Unique slug for page with request form
     */
    public static $page_slug = 'write';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register club post type
        add_action( 'init', array( __CLASS__, 'register_type' ) );

        // Update archive caption description
        add_filter( 'get_the_archive_description', array( __CLASS__, 'update_archive_description' ), 12 );

        // Update archive caption title
        add_filter( 'get_the_archive_title', array( __CLASS__, 'update_archive_title' ), 12 );

        // Add club post type to archives
        add_action( 'pre_get_posts', array( __CLASS__, 'update_archives' ), 12 );

        // Prepend author meta to content
        add_filter( 'the_content', array( __CLASS__, 'insert_author_link' ) );

        // Append club link to post content
        add_filter( 'the_content', array( __CLASS__, 'insert_club_link' ) );
    }

    /**
     * Register club post type
     */
    public static function register_type() {
        register_post_type(
            self::$post_type,
            array(
                'labels'              => array(
                    'name'          => esc_html__( 'Клуб', 'knife-theme' ),
                    'singular_name' => esc_html__( 'Запись в клуб', 'knife-theme' ),
                    'add_new'       => esc_html__( 'Добавить запись', 'knife-theme' ),
                    'menu_name'     => esc_html__( 'Клуб', 'knife-theme' ),
                ),
                'label'               => esc_html__( 'Клуб', 'knife-theme' ),
                'description'         => __( 'Клуб — это сообщество друзей «Ножа», <br>которым есть что сказать.', 'knife-theme' ),
                'supports'            => array( 'title', 'thumbnail', 'revisions', 'editor', 'excerpt', 'author', 'comments' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 10,
                'menu_icon'           => 'dashicons-groups',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'taxonomies'          => array( 'post_tag' ),
                'capability_type'     => array( 'club_item', 'club_items' ),
                'map_meta_cap'        => true,
            )
        );
    }

    /**
     * Add button to description
     */
    public static function update_archive_description( $description ) {
        if ( is_post_type_archive( self::$post_type ) ) {
            $button_link = sprintf(
                '<div class="caption__button caption__button--club"><a class="button" href="%2$s">%1$s</a></div>',
                esc_html__( 'Присоединиться', 'knife-theme' ),
                trailingslashit( home_url( self::$page_slug ) )
            );

            $description = $description . $button_link;
        }

        return $description;
    }

    /**
     * Update post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title( $title ) {
        if ( is_post_type_archive( self::$post_type ) ) {
            $title = sprintf(
                '<h1 class="caption__title caption__title--club">%s</h1>',
                post_type_archive_title( '', false )
            );
        }

        return $title;
    }

    /**
     * Insert link to author on single club post
     */
    public static function insert_author_link( $content ) {
        if ( ! is_singular( self::$post_type ) || ! in_the_loop() ) {
            return $content;
        }

        $post_id = get_the_ID();

        if ( ! property_exists( 'Knife_Authors_Manager', 'meta_authors' ) ) {
            return $content;
        }

        $users = (array) get_post_meta( $post_id, Knife_Authors_Manager::$meta_authors );

        if ( count( $users ) < 1 ) {
            return $content;
        }

        $user = get_userdata( $users[0] );

        // Allowed description tags
        $allowed = array(
            'a' => array(
                'href'   => true,
                'target' => true,
                'title'  => true,
            ),
        );

        $author = array();

        // Add author name with link
        $author[] = sprintf(
            '<div class="author__name"><strong>%s</strong>%s</div>',
            sprintf(
                __( 'Автор <a href="%1$s">%2$s</a>', 'knife-theme' ),
                esc_url( get_author_posts_url( $user->ID, $user->user_nicename ) ),
                esc_html( $user->display_name )
            ),
            sprintf(
                '<p class="author__description">%s</p>',
                wp_kses( get_the_author_meta( 'description', $user->ID ), $allowed )
            )
        );

        // Add photo if exists
        $photo = get_user_meta( $user->ID, '_knife-user-photo', true );

        if ( strlen( $photo ) > 0 ) {
            $author[] = sprintf(
                '<img class="author__photo" src="%2$s" alt="%1$s">',
                esc_html( $user->display_name ),
                esc_url( $photo )
            );
        }

        $output = sprintf( '<div class="author">%s</div>', implode( "\n", $author ) );

        return $output . $content;
    }

    /**
     * Insert user club promo link to content
     *
     * @since 1.4
     */
    public static function insert_club_link( $content ) {
        if ( is_singular( self::$post_type ) && in_the_loop() ) {
            $promo_link = sprintf(
                '<figure class="figure figure--club"><a class="button" href="%2$s">%1$s</a>',
                esc_html__( 'Присоединиться к клубу', 'knife-theme' ),
                trailingslashit( site_url( self::$page_slug ) )
            );

            $content = $content . $promo_link;
        }

        return $content;
    }

    /**
     * Append club posts to author and tag archives
     */
    public static function update_archives( $query ) {
        if ( is_admin() || ! $query->is_main_query() ) {
            return false;
        }

        if ( $query->is_tag() || $query->is_author() ) {
            $types = $query->get( 'post_type' );

            if ( ! is_array( $types ) ) {
                $types = array( 'post' );
            }

            $types[] = self::$post_type;

            $query->set( 'post_type', $types );
        }
    }
}


/**
 * Load module
 */
Knife_Club_Section::load_module();
