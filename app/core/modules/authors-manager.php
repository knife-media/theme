<?php
/**
 * Guest authors manager
 *
 * Allow mutiple guest authors per post
 *
 * @package knife-theme
 * @since 1.11
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Authors_Manager {
    /**
     * Post meta to store authors array
     *
     * @access  public
     * @var     string
     */
    public static $meta_authors = '_knife-authors';

    /**
     * Post meta to store editor value
     *
     * @access  public
     * @var     string
     * @since   1.14
     */
    public static $meta_editor = '_knife-editor';

    /**
     * Default post type lead text availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = array( 'post', 'club', 'quiz' );

    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_action = 'knife-authors-search';

    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-authors-nonce';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Create custom authors table on theme switch
        add_action( 'after_switch_theme', array( __CLASS__, 'create_table' ) );

        // Use custom table for is_author() queries
        add_filter( 'posts_clauses', array( __CLASS__, 'update_author_clauses' ), 10, 2 );

        // Add guest authors metabox
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_metabox' ) );

        // Save authors post meta
        add_action( 'save_post', array( __CLASS__, 'save_metabox' ), 10, 2 );

        // Enqueue post metabox scripts
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        // Remove default author metabox
        add_action( 'add_meta_boxes', array( __CLASS__, 'remove_authors_box' ) );

        // Suggest authors on metabox input
        add_action( 'wp_ajax_' . self::$ajax_action, array( __CLASS__, 'append_author' ) );

        // Show custom author in feeds
        add_filter( 'the_author', array( __CLASS__, 'replace_feed_author' ) );

        // Fix author query
        add_action( 'parse_query', array( __CLASS__, 'fix_author_posts' ) );

        // Update author data
        add_action( 'wp', array( __CLASS__, 'update_authordata' ) );

        // Hooks to update posts author column
        add_filter( 'manage_posts_columns', array( __CLASS__, 'filter_posts_columns' ) );
        add_filter( 'manage_pages_columns', array( __CLASS__, 'filter_posts_columns' ) );
        add_action( 'manage_posts_custom_column', array( __CLASS__, 'filter_posts_custom_column' ) );
        add_action( 'manage_pages_custom_column', array( __CLASS__, 'filter_posts_custom_column' ) );

        // Hooks to modify the published post number count on the Users List Table
        add_filter( 'manage_users_columns', array( __CLASS__, 'filter_users_columns' ) );
        add_filter( 'manage_users_custom_column', array( __CLASS__, 'filter_users_custom_column' ), 10, 3 );

        // Remove email column on the Users List Table
        add_filter( 'manage_users_columns', array( __CLASS__, 'remove_email_column' ) );

        // Show corrent user posts counter
        add_filter( 'get_usernumposts', array( __CLASS__, 'count_user_posts' ), 10, 2 );

        add_action(
            'user_profile_update_errors',
            function( $errors ) {
                $errors->remove( 'empty_email' );
            },
            10,
            3
        );

        add_filter(
            'pre_user_email',
            function( $email ) {
                if ( empty( $email ) ) {
                    $email = uniqid() . '@knife.support';
                }

                return $email;
            }
        );
    }

    /**
     * Use custom table for is_author() queries
     *
     * @since 1.17
     */
    public static function update_author_clauses( $clauses, $query ) {
        global $wpdb;

        if ( ! $query->is_author() ) {
            return $clauses;
        }

        $author = $query->queried_object_id;

        $clauses['join']  = $clauses['join'] . " JOIN {$wpdb->prefix}authors on {$wpdb->prefix}authors.post_id = {$wpdb->posts}.ID ";
        $clauses['where'] = $clauses['where'] . sprintf( "AND {$wpdb->prefix}authors.user_id = %d", absint( $author ) );

        return $clauses;
    }

    /**
     * Create custom authors table to store post_id -> user_id relation
     *
     * @since 1.17
     */
    public static function create_table() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}authors (
            id int(11) NOT NULL AUTO_INCREMENT,
            post_id int(11) NOT NULL,
            user_id int(11) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY post_id_user_id (post_id, user_id)
        )
        DEFAULT CHARACTER SET {$wpdb->charset}";

        // We do not use dbDelta here cause of DESCRIBE error
        $wpdb->query( $query ); // phpcs:ignore
    }

    /**
     * Update author data for author archive
     *
     * @link https://github.com/WordPress/WordPress/blob/e4fa7aaecdabe16ad8a8dd5dd16936f89491cc37/wp-includes/class-wp.php#L640
     */
    public static function update_authordata() {
        global $wp_query;

        if ( $wp_query->is_author() ) {
            $GLOBALS['authordata'] = get_userdata( get_queried_object_id() ); // phpcs:ignore
        }
    }

    /**
     * Add author metabox for editors
     */
    public static function add_metabox() {
        add_meta_box(
            'knife-authors-metabox',
            esc_html__( 'Авторы', 'knife-theme' ),
            array( __CLASS__, 'display_metabox' ),
            self::$post_type,
            'side'
        );
    }

    /**
     * Save authors post meta
     */
    public static function save_metabox( $post_id, $post ) {
        if ( isset( $_POST['_inline_edit'] ) ) {
            $verify = wp_verify_nonce( sanitize_key( $_POST['_inline_edit'] ), 'inlineeditnonce' );

            if ( $verify ) {
                return;
            }
        }

        if ( ! in_array( get_post_type( $post_id ), self::$post_type, true ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Delete editor field
        delete_post_meta( $post_id, self::$meta_editor );

        if ( isset( $_REQUEST[ self::$meta_editor ] ) ) {
            $editor_escaped = sanitize_text_field( wp_unslash( $_REQUEST[ self::$meta_editor ] ) );

            // Check if editor value is valid
            if ( array_key_exists( $editor_escaped, self::get_editors() ) ) {
                add_post_meta( $post_id, self::$meta_editor, $editor_escaped );
            }
        }

        // Delete all authors values
        self::delete_post_authors( $post_id );

        // Add post author if empty array
        if ( empty( $_REQUEST[ self::$meta_authors ] ) ) {
            return self::add_post_author( $post_id, $post->post_author );
        }

        $authors = (array) wp_unslash( $_REQUEST[ self::$meta_authors ] ); // phpcs:ignore

        // Add authors array from metabox input
        foreach ( $authors as $author ) {
            self::add_post_author( $post_id, absint( $author ) );
        }
    }

    /**
     * Enqueue admin side assets
     */
    public static function enqueue_assets( $hook ) {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Add assets for metabox only
        if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
            // Auto suggest
            wp_enqueue_script( 'suggest' );

            // Insert metabox styles
            wp_enqueue_style( 'knife-authors-metabox', $include . '/styles/authors-metabox.css', array(), $version );

            // Insert metabox scripts
            wp_enqueue_script( 'knife-authors-metabox', $include . '/scripts/authors-metabox.js', array( 'jquery' ), $version, true );

            $options = array(
                'meta'   => esc_attr( self::$meta_authors ),
                'action' => esc_attr( self::$ajax_action ),
                'nonce'  => wp_create_nonce( self::$ajax_nonce ),

                'error'  => esc_html__( 'Непредвиденная ошибка сервера', 'knife-theme' ),
                'verify' => esc_html__( 'Создать нового гостевого автора?', 'knife-theme' ),
            );

            wp_localize_script( 'knife-authors-metabox', 'knife_authors_metabox', $options );
        }

        // Insert common styles
        wp_enqueue_style( 'knife-authors-common', $include . '/styles/authors-common.css', array(), $version );

        // Add assets for user screen only
        if ( in_array( $hook, array( 'user-edit.php', 'user-new.php' ), true ) ) {
            wp_enqueue_script( 'knife-authors-user', $include . '/scripts/authors-user.js', array( 'jquery' ), $version, true );
        }
    }

    /**
     * Display authors metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once $include . '/templates/authors-metabox.php';
    }

    /**
     * Removes the standard WordPress 'Author' box.
     */
    public static function remove_authors_box() {
        remove_meta_box( 'authordiv', get_post_type(), 'normal' );
    }

    /**
     * Suggest authors on metabox input
     */
    public static function append_author() {
        if ( isset( $_REQUEST['q'] ) ) {
            return self::suggest_authors( sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) );
        }

        check_admin_referer( self::$ajax_nonce, 'nonce' );

        if ( empty( $_REQUEST['author'] ) ) {
            return;
        }

        // Get author field
        $author = trim( sanitize_text_field( wp_unslash( $_REQUEST['author'] ) ) );

        // Parse fisrt and last names
        list($first, $last) = explode( ' ', $author, 2 );

        // Sanitize login
        $login = sanitize_title( $author );

        // Try to find duplicate login
        if ( get_user_by( 'login', $login ) ) {
            wp_send_json_error( esc_html__( 'Пользователь с таким логином уже существует', 'knife-theme' ) );
        }

        $args = array(
            'user_login'    => $login,
            'user_nicename' => $login,
            'display_name'  => $author,
            'first_name'    => $first,
            'last_name'     => $last,
            'role'          => 'subscriber',
            'user_pass'     => wp_generate_password(),
        );

        // Create new user
        $user_id = wp_insert_user( $args );

        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( $user_id->get_error_message() );
        }

        wp_send_json_success(
            array(
                'id'     => $user_id,
                'author' => $author,
            )
        );
    }

    /**
     * Replace author field for feeds
     */
    public static function replace_feed_author( $name ) {
        if ( is_feed() && ! is_admin() ) {
            $authors = self::get_post_authors( get_the_ID() );

            if ( ! empty( $authors ) ) {
                $output = array();

                foreach ( $authors as $author ) {
                    $user = get_userdata( $author );

                    if ( isset( $user->display_name ) ) {
                        $output[] = esc_html( $user->display_name );
                    }
                }

                $name = implode( ', ', $output );
            }
        }

        return $name;
    }

    /**
     * Fix post list by author
     */
    public static function fix_author_posts( $query ) {
        if ( ! $query->is_author() ) {
            return;
        }

        $author = $query->get( 'author' );

        if ( empty( $author ) ) {
            $user = get_user_by( 'slug', $query->get( 'author_name' ) );

            // Set id by author name
            if ( isset( $user->ID ) ) {
                $author = $user->ID;
            }

            $query->set( 'author_name', null );
        }

        $query->set( 'author', null );

        // Update queried object
        $query->queried_object_id = $author;
        $query->queried_object    = get_userdata( $author );
    }

    /**
     * Add guest authors column to post lists
     */
    public static function filter_posts_columns( $columns ) {
        $new_columns = array();

        foreach ( $columns as $key => $value ) {
            $new_columns[ $key ] = $value;

            if ( $key === 'title' ) {
                $new_columns['authors-name'] = esc_html__( 'Авторы', 'knife-theme' );
            }

            if ( $key === 'author' ) {
                unset( $new_columns[ $key ] );
            }
        }

        return $new_columns;
    }

    /**
     * Add custom authors to 'authors' column on edit pages
     */
    public static function filter_posts_custom_column( $column ) {
        if ( $column === 'authors-name' ) {
            global $post;

            // Get authors
            $authors = self::get_post_authors( get_the_ID() );

            if ( empty( $authors ) ) {
                $authors[] = $post->post_author;
            }

            $output = array();

            foreach ( $authors as $author ) {
                $user = get_userdata( $author );

                // Create args array for url composing
                $args = array(
                    'post_type' => $post->post_type,
                    'author'    => $user->ID,
                );

                $output[] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
                    esc_html( $user->display_name )
                );
            }

            echo implode( ', ', $output ); // phpcs:ignore
        }
    }

    /**
     * Unset email column from users table
     */
    public static function remove_email_column( $columns ) {
        unset( $columns['email'] );

        return $columns;
    }

    /**
     * Unset the post count column because it's going to be inaccurate and provide our own
     */
    public static function filter_users_columns( $columns ) {
        $new_columns = array();

        // Unset and add our column while retaining the order of the columns
        foreach ( $columns as $name => $title ) {
            if ( $name === 'posts' ) {
                $new_columns['authors-count'] = esc_html__( 'Записи', 'knife-theme' );
                continue;
            }

            $new_columns[ $name ] = $title;
        }

        return $new_columns;
    }

    /**
     * Provide an accurate count when looking up the number of published posts for a user
     */
    public static function filter_users_custom_column( $output, $column, $user_id ) {
        if ( $column !== 'authors-count' ) {
            return $output;
        }

        // Direct call is much faster than using filter
        $count = (int) self::count_user_posts( 0, $user_id );

        if ( $count === 0 ) {
            return $count;
        }

        $value = sprintf(
            '<a href="%s" class="edit">%d</a>',
            esc_url( add_query_arg( array( 'author' => $user_id ), admin_url( 'edit.php' ) ) ),
            absint( $count )
        );

        return $output . $value;
    }

    /**
     * Filter the count_users_posts() core function to include our correct count.
     */
    public static function count_user_posts( $count, $user_id ) {
        global $wpdb;

        // phpcs:ignore
        $results = $wpdb->get_row(
            $wpdb->prepare( "SELECT COUNT(*) as counts FROM {$wpdb->prefix}authors WHERE user_id = %d", $user_id )
        );

        return $results->counts;
    }

    /**
     * Return reserverd editors list
     */
    public static function get_editors() {
        $editors = array(
            'artem-chapaev'    => 'Артем Чапаев',
            'nastya-travkina'  => 'Настя Травкина',
            'seroe-fioletovoe' => 'Серое Фиолетовое',
            'fragments'        => 'Фрагменты',
            'tania-cohen'      => 'Таня Коэн',
            'ivan'             => 'Ваня',
            'stas'             => 'Стас',
            'translations'     => 'Переводы',
        );

        return $editors;
    }

    /**
     * Get authors by post id
     *
     * @since 1.17
     */
    public static function get_post_authors( $post_id ) {
        global $wpdb;

        // Try to get cached value
        $authors = wp_cache_get( $post_id, 'knife-post-authors' );

        if ( $authors !== false ) {
            return $authors;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $results = $wpdb->get_results(
            $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}authors WHERE post_id = %d", $post_id )
        );

        $authors = array();

        foreach ( $results as $result ) {
            $authors[] = $result->user_id;
        }

        wp_cache_set( $post_id, $authors, 'knife-post-authors' );

        return $authors;
    }

    /**
     * Add post author to custom table by post id
     *
     * @since 1.17
     */
    public static function add_post_author( $post_id, $user_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $wpdb->query(
            $wpdb->prepare( "INSERT INTO {$wpdb->prefix}authors (post_id, user_id) VALUES (%d, %d)", array( $post_id, $user_id ) )
        );
    }

    /**
     * Delete post authors from custom table
     *
     * @since 1.17
     */
    public static function delete_post_authors( $post_id ) {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $wpdb->query(
            $wpdb->prepare( "DELETE FROM {$wpdb->prefix}authors WHERE post_id = %d", $post_id )
        );
    }

    /**
     * Suggest authors on ajax request
     */
    private static function suggest_authors( $query ) {
        $search = strtolower( $query );

        $args = array(
            'number'         => 10,
            'fields'         => array( 'ID', 'display_name' ),
            'search'         => sprintf( '*%s*', $search ),
            'search_columns' => array(
                'ID',
                'display_name',
                'user_email',
                'user_login',
            ),
        );

        $users = get_users( $args );

        foreach ( $users as $user ) {
            printf( "<i>%d:</i>%s\n", absint( $user->ID ), esc_html( $user->display_name ) );
        }

        if ( count( $users ) < 1 ) {
            printf( "<i>+</i>%s<b>%s</b>\n", esc_html__( 'Создать:', 'knife-theme' ), esc_html( $search ) );
        }

        wp_die();
    }
}

/**
 * Load current module environment
 */
Knife_Authors_Manager::load_module();
