<?php
/**
 * Special projects
 *
 * Custom special projects taxonomy settings
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Special_Projects {
    /**
     * Unique slug using for taxonomy register and url
     *
     * @access  public
     * @var     string
     * @since   1.3
     */
    public static $taxonomy = 'special';

    /**
     * Default post type with special taxonomy
     *
     * @access  public
     * @var     array
     * @since   1.10
     */
    public static $post_type = array( 'post', 'quiz' );

    /**
     * Unique meta to store custom term color
     *
     * @access  private
     * @var     public
     * @since   1.4
     */
    public static $term_meta = '_knife-special-options';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Register taxonomy
        add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );

        // Add special options form fields
        add_action( 'admin_init', array( __CLASS__, 'add_options_fields' ) );

        // Update archive caption title
        add_filter( 'get_the_archive_title', array( __CLASS__, 'update_archive_title' ), 15 );
    }

    /**
     * Create custom taxonomy
     */
    public static function register_taxonomy() {
        register_taxonomy(
            self::$taxonomy,
            self::$post_type,
            array(
                'labels'            => array(
                    'name'                       => esc_html__( 'Спецпроекты', 'knife-theme' ),
                    'singular_name'              => esc_html__( 'Спецпроект', 'knife-theme' ),
                    'search_items'               => esc_html__( 'Поиск', 'knife-theme' ),
                    'popular_items'              => esc_html__( 'Популярные спецпроекты', 'knife-theme' ),
                    'all_items'                  => esc_html__( 'Все', 'knife-theme' ),
                    'edit_item'                  => esc_html__( 'Редактировать', 'knife-theme' ),
                    'update_item'                => esc_html__( 'Обновить', 'knife-theme' ),
                    'add_new_item'               => esc_html__( 'Добавить новый', 'knife-theme' ),
                    'new_item_name'              => esc_html__( 'Новый спецпроект', 'knife-theme' ),
                    'separate_items_with_commas' => esc_html__( 'Разделить записи запятыми', 'knife-theme' ),
                    'add_or_remove_items'        => esc_html__( 'Добавить или удалить тип', 'knife-theme' ),
                    'choose_from_most_used'      => esc_html__( 'Наиболее используемые', 'knife-theme' ),
                    'not_found'                  => esc_html__( 'Не найдено', 'knife-theme' ),
                    'menu_name'                  => esc_html__( 'Спецпроекты', 'knife-theme' ),
                ),
                'public'            => true,
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => true,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => self::$taxonomy ),
                'meta_box_cb'       => array( __CLASS__, 'print_metabox' ),
            )
        );
    }

    /**
     * Add adminside edit form fields
     *
     * @since 1.4
     */
    public static function add_options_fields() {
        // Print special options row
        add_action( self::$taxonomy . '_edit_form_fields', array( __CLASS__, 'print_options_row' ), 9 );

        // Save admin side options meta
        add_action( 'edited_' . self::$taxonomy, array( __CLASS__, 'save_options_meta' ) );

        // Enqueue scripts only on admin screen
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_options_scripts' ) );
    }

    /**
     * Display custom special options row
     *
     * @since 1.4
     */
    public static function print_options_row( $term ) { // phpcs:ignore
        $include = get_template_directory() . '/core/include';

        include_once $include . '/templates/special-options.php';
    }

    /**
     * Enqueue assets to term edit screen only
     *
     * @since 1.4
     */
    public static function add_options_scripts( $hook ) {
        $screen = get_current_screen()->taxonomy;

        if ( $hook !== 'term.php' || $screen !== self::$taxonomy ) {
            return;
        }

        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style( 'wp-color-picker' );

        // Insert admin scripts
        wp_enqueue_script( 'knife-special-options', $include . '/scripts/special-options.js', array( 'jquery', 'wp-color-picker' ), $version, true );
    }

    /**
     * Add custom taxonomy metabox without tabs and add-new feature
     *
     * @since 1.5
     */
    public static function print_metabox( $post, $box ) { // phpcs:ignore
        $include = get_template_directory() . '/core/include';

        include_once $include . '/templates/special-metabox.php';
    }

    /**
     * Save color meta
     *
     * @since 1.4
     */
    public static function save_options_meta( $term_id ) {
        if ( ! current_user_can( 'edit_term', $term_id ) ) {
            return;
        }

        if ( empty( $_REQUEST[ self::$term_meta ] ) ) {
            return delete_term_meta( $term_id, self::$term_meta );
        }

        $data = wp_unslash( $_REQUEST[ self::$term_meta ] ); // phpcs:ignore

        // Sanitize meta
        if ( isset( $data['single'] ) ) {
            $meta['single'] = sanitize_hex_color( $data['single'] );
        }

        if ( isset( $data['hidden'] ) ) {
            $meta['hidden'] = absint( $data['hidden'] );
        }

        update_term_meta( $term_id, self::$term_meta, $meta );
    }

    /**
     * Update special post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title( $title ) {
        if ( is_tax( self::$taxonomy ) ) {
            $term_id = get_queried_object_id();

            // Get term meta
            $options = get_term_meta( $term_id, self::$term_meta, true );

            if ( empty( $options['archive'] ) ) {
                $title = sprintf(
                    '<h1 class="caption__title caption__title--%2$s">%1$s</h1>',
                    single_term_title( '', false ),
                    esc_attr( self::$taxonomy )
                );

                return $title;
            }

            $styles = array(
                'color:' . $options['archive'],
            );

            $title = sprintf(
                '<h1 class="caption__title caption__title--%2$s" style="%3$s">%1$s</h1>',
                single_term_title( '', false ),
                esc_attr( self::$taxonomy ),
                esc_attr( implode( '; ', $styles ) )
            );
        }

        return $title;
    }
}


/**
 * Load current module environment
 */
Knife_Special_Projects::load_module();
