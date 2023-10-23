<?php
/**
 * Subscribe letters table class
 *
 * @package knife-theme
 * @since 1.17
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Knife_Subscribe_Letters_Table extends WP_List_Table {
    /**
     * Subscribe link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private $db = null;

    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private $per_page = '';

    /**
     * Parent tools page slug
     *
     * @access  private
     * @var     string
     */
    private $admin_url = '';

    /**
     * Subscribe letters table constructor
     */
    public function __construct( $db, $per_page, $admin_url ) {
        parent::__construct(
            array(
                'plural' => 'subscribe-letters',
                'ajax'   => false,
            )
        );

        $this->db        = $db;
        $this->per_page  = $per_page;
        $this->admin_url = $admin_url;
    }

    /**
     * Default column render
     */
    public function column_default( $item, $column_name ) {
        if ( $item['status'] === 'sent' ) {
            return absint( $item[ $column_name ] );
        }

        return '-';
    }

    /**
     * Checkbox column render
     */
    public function column_cb( $item ) {
        $markup = sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            absint( $item['id'] )
        );

        return $markup;
    }

    /**
     * Title column render
     */
    public function column_title( $item ) {
        $status = null;

        if ( $item['status'] === 'draft' ) {
            $status = esc_html__( ' — Черновик', 'knife-theme' );
        }

        if ( $item['status'] === 'scheduled' ) {
            $status = esc_html__( ' — Запланировано', 'knife-theme' );
        }

        $demolink = null;

        if ( isset( KNIFE_SUBSCRIBE['url'] ) ) {
            $demolink = sprintf( rtrim( KNIFE_SUBSCRIBE['url'], '/' ) . '/preview/%d', absint( $item['id'] ) );
        }

        $markup = sprintf(
            '<strong class="row-title"><a href="%s" target="_blank" rel="noopener">%s</a>%s</strong>',
            esc_url( $demolink ),
            esc_html( $item['title'] ),
            $status
        );

        $actions = array();

        $actions['view'] = sprintf(
            '<a href="%s" target="_blank" rel="noopener">%s</a>',
            esc_url( $demolink ),
            esc_html__( 'Просмотреть', 'knife-theme' )
        );

        if ( $item['status'] === 'draft' ) {
            $args = array( 'id' => $item['id'] );

            $actions['edit'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( add_query_arg( $args, $this->admin_url ) ),
                esc_html__( 'Редактировать', 'knife-theme' )
            );
        }

        if ( $item['status'] === 'sent' ) {
            $args = array(
                'tab' => 'stats',
                'id'  => $item['id'],
            );

            $actions['stats'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( add_query_arg( $args, $this->admin_url ) ),
                esc_html__( 'Статистика', 'knife-theme' )
            );
        }

        return $markup . $this->row_actions( $actions, false );
    }

    /**
     * Fix created format
     */
    public function column_released( $item ) {
        if ( $item['status'] === 'draft' ) {
            return '-';
        }

        $released = date_i18n( __( 'd.m.Y в H:i', 'knife-theme' ), strtotime( $item['released'] ) );

        if ( $item['status'] === 'sent' ) {
            return $released;
        }

        $actions = array();

        if ( $item['status'] === 'scheduled' ) {
            $args = array(
                'action' => 'cancel',
                'id'     => $item['id'],
            );

            $actions['stats'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( add_query_arg( $args, $this->admin_url ) ),
                esc_html__( 'Отменить рассылку', 'knife-theme' )
            );
        }

        return $released . $this->row_actions( $actions, true );
    }

    /**
     * Get columns to show in the list table.
     */
    public function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'title'        => esc_html__( 'Название', 'knife-theme' ),
            'released'     => esc_html__( 'Дата рассылки', 'knife-theme' ),
            'opens'        => esc_html__( 'Открытия', 'knife-theme' ),
            'clicks'       => esc_html__( 'Переходы', 'knife-theme' ),
            'receive'      => esc_html__( 'Охват', 'knife-theme' ),
            'unsubscribes' => esc_html__( 'Отписки', 'knife-theme' ),
        );

        return $columns;
    }

    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $columns = array(
            'title'        => array(
                'title',
                true,
            ),

            'released'     => array(
                'released',
                true,
            ),

            'clicks'       => array(
                'clicks',
                true,
            ),

            'receive'      => array(
                'receive',
                true,
            ),

            'unsubscribes' => array(
                'unsubscribes',
                true,
            ),

            'opens'        => array(
                'opens',
                true,
            ),
        );

        return $columns;
    }

    /**
     * Return array of bulk actions
     */
    public function get_bulk_actions() {
        $actions = array(
            'delete' => esc_html__( 'Удалить', 'knife-theme' ),
        );

        return $actions;
    }

    /**
     * Get rows from database and prepare them to be showed in table
     */
    public function prepare_items() {
        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
        );

        $db = $this->db;

        $args = array(
            'orderby'     => 'id',
            'order'       => 'DESC',
            'paged'       => 0,
            'where'       => '1=1',
            'per_page'    => 20,
            'total_items' => 0,
        );

        $args['per_page'] = $this->get_items_per_page( $this->per_page );

        if ( isset( $_REQUEST['paged'] ) ) {
            $args['paged'] = max( 0, intval( $_REQUEST['paged'] - 1 ) * $args['per_page'] );
        }

        if ( isset( $_REQUEST['orderby'] ) ) {
            $orderby = sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) );

            if ( array_key_exists( $orderby, $this->get_sortable_columns() ) ) {
                $args['orderby'] = $orderby;
            }
        }

        if ( isset( $_REQUEST['order'] ) ) {
            $order = strtoupper( sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) );

            if ( in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
                $args['order'] = $order;
            }
        }

        if ( ! empty( $_REQUEST['s'] ) ) {
            $args['where'] = $db->prepare(
                'letters.title LIKE %s',
                '%' . $db->esc_like( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) ) . '%'
            );
        }

        $query = 'SELECT SQL_CALC_FOUND_ROWS
            letters.id AS id,
            letters.title AS title,
            letters.status AS status,
            letters.released AS released,
            letters.updated AS updated,
            IFNULL(SUM(action = "click"), 0) clicks,
            IFNULL(SUM(action = "open"), 0) opens,
            IFNULL(SUM(action = "receive"), 0) receive,
            IFNULL(SUM(action = "unsubscribe"), 0) unsubscribes
            FROM letters
            LEFT JOIN actions ON letters.id = actions.letters_id
            WHERE %s
            GROUP BY letters.id
            ORDER BY %s %s
            LIMIT %d OFFSET %d';

        $this->items = $db->get_results(
            sprintf( $query, $args['where'], $args['orderby'], $args['order'], $args['per_page'], $args['paged'] ),
            ARRAY_A
        );

        if ( ! $db->last_error ) {
            $args['total_items'] = $db->get_var( 'SELECT FOUND_ROWS()' );
        }

        $this->set_pagination_args(
            array(
                'total_items' => $args['total_items'],
                'per_page'    => $args['per_page'],
            )
        );
    }
}
