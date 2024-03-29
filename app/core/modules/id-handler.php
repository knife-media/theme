<?php
/**
 * ID handler
 *
 * Prepare data for id handler module
 *
 * @package knife-theme
 * @since 1.13
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_ID_Handler {
    /**
     * Lead text post meta
     *
     * @access  public
     * @var     string
     */
    public static $meta_question = '_knife-question';

    /**
     * Default post type with question metabox
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
    private static $ajax_request = 'knife-id-request';

    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Include required id loader data
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inject_object' ), 12 );

        // Prevent to create WordPress posts and pages with custom reserved slug
        add_filter( 'wp_unique_post_slug_is_bad_flat_slug', array( __CLASS__, 'prevent_slug' ), 10, 2 );
        add_filter( 'wp_unique_post_slug_is_bad_hierarchical_slug', array( __CLASS__, 'prevent_slug' ), 10, 2 );

        // Remove discussion options page
        add_action( 'admin_menu', array( __CLASS__, 'remove_settings' ) );

        // Add comment question metabox
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_metabox' ) );

        // Remove comments and trackbacks metaboxes
        add_action( 'add_meta_boxes', array( __CLASS__, 'remove_comments_boxes' ) );

        // Save meta
        add_action( 'save_post', array( __CLASS__, 'save_metabox' ), 10, 1 );

        // Insert new post to analytics table
        add_action( 'knife_schedule_id', array( __CLASS__, 'start_task' ) );

        // Schedule insertion to id database
        add_action( 'transition_post_status', array( __CLASS__, 'schedule_insertion' ), 10, 3 );

        // Enqueue metabox scripts
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

        // Handle admin requests
        add_action( 'wp_ajax_' . self::$ajax_request, array( __CLASS__, 'handle_request' ) );

        // Define id settings if still not
        if ( ! defined( 'KNIFE_ID' ) ) {
            define( 'KNIFE_ID', array() );
        }
    }

    /**
     * Add comment question metabox
     *
     * @since 1.14
     */
    public static function add_metabox() {
        add_meta_box( 'knife-question-metabox', esc_html__( 'Вопрос для комментариев', 'knife-theme' ), array( __CLASS__, 'display_metabox' ), self::$post_type, 'advanced', 'high' );
    }

    /**
     * Remove default comments and trackback  metaboxes
     *
     * @since 1.16
     */
    public static function remove_comments_boxes() {
        remove_meta_box( 'commentsdiv', get_post_type(), 'normal' );
        remove_meta_box( 'trackbacksdiv', get_post_type(), 'normal' );
    }

    /**
     * Start scheduled task
     *
     * @since 1.16
     */
    public static function start_task( $post_id ) {
        $status = get_post_status( $post_id );

        // Mix with default values
        $conf = wp_parse_args(
            KNIFE_ID,
            array(
                'host'     => DB_HOST,
                'name'     => DB_NAME,
                'user'     => DB_USER,
                'password' => DB_PASSWORD,
            )
        );

        // Create custom db connection
        $db = new wpdb( $conf['user'], $conf['password'], $conf['name'], $conf['host'] );
        $db->hide_errors();

        $slug  = wp_make_link_relative( get_permalink( $post_id ) );
        $title = get_the_title( $post_id );

        if ( $status === 'publish' ) {
            return $db->replace( 'posts', compact( 'post_id', 'slug', 'title' ) );
        }
    }

    /**
     * Schedule new post to id database
     *
     * @since 1.16
     */
    public static function schedule_insertion( $new_status, $old_status, $post ) {
        wp_schedule_single_event( time() + 60, 'knife_schedule_id', array( $post->ID ) );
    }

    /**
     * Enqueue assets for metabox
     *
     * @since 1.14
     */
    public static function enqueue_assets( $hook ) {
        global $post;

        if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
            return;
        }

        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // Current screen object
        $screen = get_current_screen();

        if ( ! in_array( $screen->post_type, self::$post_type, true ) ) {
            return;
        }

        // Insert admin styles
        wp_enqueue_style( 'knife-question-metabox', $include . '/styles/question-metabox.css', array(), $version );

        // Insert admin scripts
        wp_enqueue_script( 'knife-question-metabox', $include . '/scripts/question-metabox.js', array( 'jquery' ), $version, true );
    }


    /**
     * Display comment question metabox
     *
     * @since 1.14
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once $include . '/templates/question-metabox.php';
    }

    /**
     * Save question metabox
     *
     * @since 1.14
     */
    public static function save_metabox( $post_id ) {
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

        if ( ! isset( $_REQUEST[ self::$meta_question ] ) ) {
            return;
        }

        $question = array_filter(
            array_map( 'sanitize_text_field', wp_unslash( $_REQUEST[ self::$meta_question ] ) )
        );

        if ( empty( $question ) ) {
            return delete_post_meta( $post_id, self::$meta_question );
        }

        update_post_meta( $post_id, self::$meta_question, $question );
    }

    /**
     * Remove discussion options page
     */
    public static function remove_settings() {
        remove_submenu_page( 'options-general.php', 'options-discussion.php' );
    }

    /**
     * Try to remove comment if needed
     */
    private static function remove_comment( $db, $fields ) {
        if ( empty( $fields['remove'] ) ) {
            return;
        }

        $comment = absint( $fields['remove'] );

        // Try to remove comment
        $remove = $db->update( 'comments', array( 'status' => 'removed' ), array( 'id' => $comment ) );

        if ( ! $remove ) {
            wp_send_json_error( esc_html__( 'Не удалось удалить комментарий', 'knife-theme' ) );
        }

        wp_send_json_success();
    }

    /**
     * Try to block user and remove his comments
     */
    private static function block_user( $db, $fields ) {
        if ( empty( $fields['block'] ) ) {
            return;
        }

        $comment = absint( $fields['block'] );

        // Get user id
        $user_id = $db->get_var(
            $db->prepare( 'SELECT user_id FROM comments WHERE id = %d', $comment )
        );

        if ( $user_id === null ) {
            wp_send_json_error( esc_html__( 'Не удалось найти пользователя', 'knife-theme' ) );
        }

        // Set blocked status to user
        $blocked = $db->update( 'users', array( 'status' => 'blocked' ), array( 'id' => $user_id ) );

        if ( $blocked === false ) {
            wp_send_json_error( esc_html__( 'Не удалось заблокировать пользователя', 'knife-theme' ) );
        }

        // Remove all user comments
        $remove = $db->update( 'comments', array( 'status' => 'removed' ), array( 'user_id' => $user_id ) );

        if ( $remove === false ) {
            wp_send_json_error( esc_html__( 'Не удалось удалить комментарии пользователя', 'knife-theme' ) );
        }

        wp_send_json_success();
    }

    /**
     * Handle admin requests
     */
    public static function handle_request() {
        if ( ! check_ajax_referer( self::$ajax_request, 'nonce', false ) ) {
            wp_send_json_error( esc_html__( 'Ошибка безопасности. Попробуйте еще раз', 'knife-theme' ) );
        }

        if ( ! current_user_can( 'publish_posts' ) ) {
            wp_send_json_error( esc_html__( 'Для вашего пользователя это действие недоступно', 'knife-theme' ) );
        }

        // Mix with default values
        $conf = wp_parse_args(
            KNIFE_ID,
            array(
                'host'     => DB_HOST,
                'name'     => DB_NAME,
                'user'     => DB_USER,
                'password' => DB_PASSWORD,
            )
        );

        // Create custom db connection
        $db = new wpdb( $conf['user'], $conf['password'], $conf['name'], $conf['host'] );
        $db->hide_errors();

        if ( isset( $db->error ) ) {
            wp_send_json_error( esc_html__( 'Не удалось соединиться с удаленной базой данных', 'knife-theme' ) );
        }

        // Try to remove comment
        self::remove_comment( $db, wp_unslash( $_REQUEST ) );

        // Try to block user
        self::block_user( $db, wp_unslash( $_REQUEST ) );
    }

    /**
     * Prevent to create WordPress posts and pages with custom reserved slug
     */
    public static function prevent_slug( $bad_slug, $slug ) {
        if ( $slug === 'id' ) {
            return true;
        }

        return $bad_slug;
    }

    /**
     * Generate localize policy link
     */
    public static function get_policy_link() {
        $policy = sprintf(
            __( 'Авторизуясь, вы соглашаетесь с условиями  <a href="%s" target="_blank">пользовательского соглашения</a> и даете согласие на обработку персональных данных.', 'knife-theme' ),
            esc_url( get_permalink( get_page_by_path( '/user-agreement/' ) ) )
        );

        return $policy;
    }

    /**
     * Include required id loader data
     */
    public static function inject_object() {
        if ( ! is_singular() ) {
            return;
        }

        $post_id = get_queried_object_id();

        $options = array(
            'post'          => $post_id,

            'form'          => array(
                'submit'      => esc_html__( 'Написать', 'knife-theme' ),
                'authorize'   => esc_html__( 'Войти и написать', 'knife-theme' ),
                'placeholder' => esc_html__( 'Что вы об этом думаете?', 'knife-theme' ),
                'reply'       => esc_html__( 'Напишите свой ответ…', 'knife-theme' ),
                'cancel'      => esc_html__( 'Отменить', 'knife-theme' ),
                'exit'        => esc_html__( 'Выйти из профиля', 'knife-theme' ),
                'notify'      => esc_html__( 'Посмотреть ответы на ваши комментарии', 'knife-theme' ),
            ),

            'comments'      => array(
                'anonymous' => esc_html__( 'Анонимный пользователь', 'knife-theme' ),
                'removed'   => esc_html__( 'Сообщение было удалено', 'knife-theme' ),
                'blocked'   => esc_html__( 'Пользователь заблокирован, и все его сообщения будут удалены', 'knife-theme' ),
                'reply'     => esc_html__( 'Ответить', 'knife-theme' ),
                'remove'    => esc_html__( 'Удалить', 'knife-theme' ),
                'block'     => esc_html__( 'Забанить', 'knife-theme' ),
                'expand'    => array(
                    esc_html__( 'Показать %d комментарий', 'knife-theme' ),
                    esc_html__( 'Показать %d комментария', 'knife-theme' ),
                    esc_html__( 'Показать %d комментариев', 'knife-theme' ),
                ),
                'noavatar'  => get_template_directory_uri() . '/assets/images/no-avatar.png',
            ),

            'notifications' => array(
                'common' => array(
                    wp_kses(
                        __( 'К записи %1$s добавлен <strong>%3$d новый ответ</strong> на %2$s.', 'knife-theme' ),
                        array( 'strong' => array() )
                    ),
                    wp_kses(
                        __( 'К записи %1$s добавлено <strong>%3$d новых ответа</strong> на %2$s.', 'knife-theme' ),
                        array( 'strong' => array() )
                    ),
                    wp_kses(
                        __( 'К записи %1$s добавлено <strong>%3$d новых ответов</strong> на %2$s.', 'knife-theme' ),
                        array( 'strong' => array() )
                    ),
                ),
                'plural' => esc_html__( 'ваши комментарии', 'knife-theme' ),
                'single' => esc_html__( 'ваш комментарий', 'knife-theme' ),
            ),

            'login'         => array(
                'policy'    => self::get_policy_link(),
                'heading'   => esc_html__( 'Войти через соцсеть', 'knife-theme' ),
                'helper'    => esc_html__( 'Для того, чтобы оставлять комментарии и ставить оценки.', 'knife-theme' ),
                'vkontakte' => esc_html__( 'ВКонтакте', 'knife-theme' ),
                'google'    => esc_html__( 'Google', 'knife-theme' ),
                'yandex'    => esc_html__( 'Яндекс', 'knife-theme' ),
                'facebook'  => esc_html__( 'Facebook', 'knife-theme' ),
            ),

            'error'         => esc_html__( 'Произошла непредвиденная ошибка. Повторите попытку позже.', 'knife-theme' ),
        );

        if ( current_user_can( 'publish_posts' ) ) {
            $options = array_merge(
                $options,
                array(
                    'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
                    'action'  => self::$ajax_request,
                    'nonce'   => wp_create_nonce( self::$ajax_request ),
                )
            );
        }

        $options['question'] = get_post_meta( $post_id, self::$meta_question, true );

        if ( empty( $options['question'] ) ) {
            $options['question'] = array();
        }

        wp_localize_script( 'knife-theme', 'knife_id_handler', $options );
    }
}


/**
 * Load current module environment
 */
Knife_ID_Handler::load_module();
