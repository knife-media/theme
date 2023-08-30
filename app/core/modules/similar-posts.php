<?php
/**
 * Similar posts
 *
 * Show similar posts grouped by common tags inside single template
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Similar_Posts {
    /**
     * Default post type with similar aside
     *
     * @access  public
     * @var     array
     * @since   1.8
     */
    public static $post_type = array( 'post', 'club', 'quiz' );

    /**
     * Cache group to store similar posts
     *
     * @access  private
     * @var     string
     */
    private static $cache_group = 'knife-similar-posts';

    /**
     * Page capability
     *
     * @access  private
     * @var     string
     * @since   1.14
     */
    private static $page_cap = 'promo_manage';

    /**
     * Settings page slug
     *
     * @access  private
     * @var     sting
     * @since   1.11
     */
    private static $page_slug = 'knife-similar';

    /**
     * Store settings page base_id screen
     *
     * @access  private
     * @var     string
     * @since   1.11
     */
    private static $screen_base = null;

    /**
     * Option to store similar promo items
     *
     * @access  private
     * @var     string
     * @since   1.11
     */
    private static $option_promo = 'knife_similar_promo';

    /**
     * Post meta to store similar exclude option
     *
     * @access  public
     * @var     string
     * @since   1.11
     */
    public static $meta_hidden = '_knife_similar_hide';

    /**
     * Categories to show in similar
     *
     * @access  private
     * @var     array
     * @since   1.12
     */
    private static $category = array( 'longreads', 'play' );

    /**
     * Post meta to store similar posts
     *
     * @access  public
     * @var     string
     * @since   1.12
     */
    public static $meta_similar = '_knife_similar';

    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include similar posts data
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inject_object' ), 12 );

        // Update links returned via tinymce
        add_filter( 'wp_link_query', array( __CLASS__, 'update_link_query' ), 12 );

        // Add similar promo settings
        add_action( 'admin_menu', array( __CLASS__, 'add_management_menu' ), 9 );

        // Process required actions for management page
        add_action( 'current_screen', array( __CLASS__, 'init_managment_page' ) );

        // Admin-post action to add similar link
        add_action( 'admin_post_' . self::$page_slug . '-append', array( __CLASS__, 'append_similar_link' ) );

        // Add option to hide similar posts
        add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'print_checkbox' ) );

        // Update hide similar posts meta
        add_action( 'save_post', array( __CLASS__, 'save_meta' ) );

        // Regenerate similar for post
        add_action( 'knife_schedule_similar', array( __CLASS__, 'generate_similar' ) );

        // Regenerate similar post publish
        add_action( 'transition_post_status', array( __CLASS__, 'create_similar' ), 10, 3 );
    }

    /**
     * Prints checkbox in post publish action section
     *
     * @since 1.11
     */
    public static function print_checkbox( $post ) {
        if ( $post->post_type !== 'post' ) {
            return;
        }

        $hidden = get_post_meta( $post->ID, self::$meta_hidden, true );

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr( self::$meta_hidden ),
            esc_html__( 'Скрыть блок похожих записей', 'knife-theme' ),
            checked( $hidden, 1, false )
        );
    }

    /**
     * Save post meta
     *
     * @since 1.11
     */
    public static function save_meta( $post_id ) {
        if ( isset( $_POST['_inline_edit'] ) ) {
            $verify = wp_verify_nonce( sanitize_key( $_POST['_inline_edit'] ), 'inlineeditnonce' );

            if ( $verify ) {
                return;
            }
        }
        if ( get_post_type( $post_id ) !== 'post' ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( empty( $_REQUEST[ self::$meta_hidden ] ) ) {
            return delete_post_meta( $post_id, self::$meta_hidden );
        }

        return update_post_meta( $post_id, self::$meta_hidden, 1 );
    }

    /**
     * Add similar promo management menu
     *
     * @since 1.11
     */
    public static function add_management_menu() {
        $hookname = add_management_page(
            esc_html__( 'Настройки промо блока рекомендаций', 'knife-theme' ),
            esc_html__( 'Блок рекомендаций', 'knife-theme' ),
            self::$page_cap,
            self::$page_slug,
            array( __CLASS__, 'display_management_page' )
        );

        // Set management page base_id screen
        self::$screen_base = $hookname;
    }

    /**
     * Process required actions for settings page
     *
     * @since 1.11
     */
    public static function init_managment_page() {
        $current_screen = get_current_screen();

        if ( $current_screen->base !== self::$screen_base ) {
            return;
        }

        if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' ) {
            self::delete_similar_link();
        }

        // Add scripts to admin page
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
    }

    /**
     * Enqueue assets to admin certain screen only
     *
     * @since 1.11
     */
    public static function enqueue_assets() {
        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style( 'knife-similar-options', $include . '/styles/similar-options.css', array(), $version );
    }

    /**
     * Display management page
     *
     * @since 1.11
     */
    public static function display_management_page() {
        $message = isset( $_REQUEST['message'] ) ? absint( $_REQUEST['message'] ) : 0;

        switch ( $message ) {
            case 1:
                add_settings_error(
                    'knife-similar-actions',
                    'append',
                    esc_html__( 'Ссылка успешно добавлена', 'knife-theme' ),
                    'updated'
                );
                break;
            case 2:
                add_settings_error(
                    'knife-similar-actions',
                    'append',
                    esc_html__( 'Не удалось добавить ссылку', 'knife-theme' )
                );
                break;
            case 3:
                add_settings_error(
                    'knife-similar-actions',
                    'delete',
                    esc_html__( 'Не удалось удалить ссылку', 'knife-theme' )
                );
                break;
        }

        $include = get_template_directory() . '/core/include';

        // Include options template
        include_once $include . '/templates/similar-options.php';
    }

    /**
     * Update links returned via tinymce
     *
     * @since 1.9
     */
    public static function update_link_query( $results ) {
        foreach ( $results as &$result ) {
            $result['title'] = html_entity_decode( $result['title'] );
        }

        return $results;
    }

    /**
     * Create similar on post publish
     *
     * @since 1.12
     */
    public static function create_similar( $new_status, $old_status, $post ) {
        if ( ! in_array( $post->post_type, self::$post_type, true ) ) {
            return;
        }

        if ( $new_status !== 'publish' ) {
            return;
        }

        // Schedule similar generation right now
        wp_schedule_single_event( time(), 'knife_schedule_similar', array( $post->ID ) );
    }

    /**
     * Inject similar posts data
     */
    public static function inject_object() {
        $post_id = get_queried_object_id();

        if ( ! is_singular( self::$post_type ) ) {
            return;
        }

        // Get similar from meta
        $meta = get_post_meta( $post_id, self::$meta_similar, true );

        // Set default meta values
        $meta = wp_parse_args(
            $meta,
            array(
                'similar' => array(),
                'updated' => 0,
            )
        );

        // Schedule updation if required
        if ( $meta['updated'] < strtotime( '-10 days' ) ) {
            wp_schedule_single_event( time() + 60, 'knife_schedule_similar', array( $post_id ) );
        }

        $hidden = true;

        if ( get_post_type( $post_id ) === 'post' ) {
            $hidden = get_post_meta( $post_id, self::$meta_hidden, true );
        }

        $options = array(
            'title'   => esc_html__( 'Читайте также', 'knife-theme' ),
            'hidden'  => absint( $hidden ),
            'similar' => $meta['similar'],
            'promo'   => self::get_promo(),
        );

        // Add similar options to page
        wp_localize_script( 'knife-theme', 'knife_similar_posts', $options );
    }

    /**
     * Append similar link to promo
     *
     * @since 1.11
     */
    public static function append_similar_link() {
        check_admin_referer( 'knife-similar-append' );

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этой странице', 'knife-theme' ) );
        }

        $admin_url = admin_url( '/tools.php?page=' . self::$page_slug );

        // Check if required values not empty
        if ( ! empty( $_POST['title'] ) && ! empty( $_POST['link'] ) ) {
            $promo = get_option( self::$option_promo, array() );

            // Add value to promo items
            $item = array(
                'link'  => sanitize_text_field( wp_unslash( $_POST['link'] ) ),
                'title' => wp_kses(
                    wp_unslash( $_POST['title'] ),
                    array(
                        'em' => array(),
                    )
                ),
            );

            if ( ! empty( $_POST['pixel'] ) ) {
                $item['pixel'] = sanitize_text_field( wp_unslash( $_POST['pixel'] ) );
            }

            if ( method_exists( 'Knife_Promo_Manager', 'rebuild_ord_link' ) ) {
                $item['link'] = Knife_Promo_Manager::rebuild_ord_link( $item['link'] );
            }

            $promo[] = $item;

            // Try to update option
            if ( update_option( self::$option_promo, $promo ) ) {
                wp_safe_redirect( add_query_arg( 'message', 1, $admin_url ) );
                exit;
            }
        }

        wp_safe_redirect( add_query_arg( 'message', 2, $admin_url ), 303 );
        exit;
    }

    /**
     * Delete similar link from promo
     *
     * @since 1.11
     */
    public static function delete_similar_link() {
        check_admin_referer( 'knife-similar-delete' );

        if ( ! current_user_can( self::$page_cap ) ) {
            wp_die( esc_html__( 'Извините, у вас нет доступа к этой странице', 'knife-theme' ) );
        }

        // Get current page admin link
        $admin_url = admin_url( '/tools.php?page=' . self::$page_slug );

        if ( ! isset( $_REQUEST['id'] ) ) {
            return;
        }

        $id = absint( $_REQUEST['id'] );

        // Get promo items from settings
        $promo = get_option( self::$option_promo, array() );

        unset( $promo[ $id ] );

        if ( update_option( self::$option_promo, $promo ) ) {
            wp_safe_redirect( $admin_url, 303 );
            exit;
        }

        wp_safe_redirect( add_query_arg( 'message', 3, $admin_url ), 303 );
        exit;
    }

    /**
     * Get similar posts related on common tags
     *
     * Using get_the_tags function to retrieve terms with primary tag first
     */
    public static function generate_similar( $post_id ) {
        $similar = array();

        // Get post tags by post id
        $post_terms = get_the_tags( $post_id );

        if ( $post_terms ) {
            $the_terms = wp_list_pluck( $post_terms, 'term_id' );

            $query_args = array(
                'posts_per_page'      => -1,
                'tag_id'              => $the_terms[0],
                'post__not_in'        => array( $post_id ),
                'post_status'         => 'publish',
                'ignore_sticky_posts' => true,
                'tax_query'           => array( // phpcs:ignore
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => self::$category,
                    ),
                ),
                'fields'              => 'ids',
            );

            $related = array();

            // Get posts with primary tag
            $the_posts = get_posts( $query_args );

            foreach ( $the_posts as $id ) {
                $related[ $id ] = 0;

                foreach ( wp_get_post_terms( $id, 'post_tag' ) as $tag ) {
                    if ( in_array( $tag->term_id, $the_terms, true ) ) {
                        $related[ $id ] = $related[ $id ] + 1;
                    }
                }
            }

            // Sort by tags count
            arsort( $related );

            // Get first 15 elements
            $related = array_slice( $related, 0, 15, true );

            foreach ( $related as $id => $count ) {
                $similar[] = array(
                    'title' => get_the_title( $id ),
                    'link'  => get_permalink( $id ),
                );
            }
        }

        $meta = array(
            'updated' => time(),
            'similar' => $similar,
        );

        update_post_meta( $post_id, self::$meta_similar, $meta );
    }

    /**
     * Append similar promo links from options
     */
    private static function get_promo() {
        return array_values( get_option( self::$option_promo, array() ) );
    }
}


/**
 * Load current module environment
 */
Knife_Similar_Posts::load_module();
