<?php
/**
 * Subscribe users table class
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

class Knife_Subscribe_Users_Table extends WP_List_Table {
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
     * Subscribe letters table constructor
     */
    public function __construct( $db, $per_page ) {
        parent::__construct(
            array(
                'plural' => 'subscribe-users',
                'ajax'   => false,
            )
        );

        $this->db       = $db;
        $this->per_page = $per_page;
    }

    /**
     * Default column render
     */
    public function column_default( $item, $column_name ) {
        return absint( $item[ $column_name ] );
    }

    /**
     * Fix created format
     */
    public function column_created( $item ) {
        return date_i18n( 'd.m.Y H:i', strtotime( $item['created'] ) );
    }

    /**
     * IP column render
     */
    public function column_ip( $item ) {
        return esc_html( $item['ip'] );
    }

    /**
     * Email column render
     */
    public function column_email( $item ) {
        return esc_html( $item['email'] );
    }

    /**
     * Status column render
     */
    public function column_status( $item ) {
        if ( $item['status'] === 'active' ) {
            return esc_html__( 'Активен', 'knife-theme' );
        }

        if ( $item['status'] === 'block' ) {
            return esc_html__( 'Заблокирован', 'knife-theme' );
        }

        if ( $item['status'] === 'unconfirmed' ) {
            return esc_html__( 'Не подтвержден', 'knife-theme' );
        }

        return esc_html__( 'Отписан', 'knife-theme' );
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
     * Get columns to show in the list table.
     */
    public function get_columns() {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'email'    => esc_html__( 'E-mail', 'knife-theme' ),
            'received' => esc_html__( 'Получено', 'knife-theme' ),
            'opens'    => esc_html__( 'Открытия', 'knife-theme' ),
            'clicks'   => esc_html__( 'Переходы', 'knife-theme' ),
            'created'  => esc_html__( 'Дата', 'knife-theme' ),
            'ip'       => esc_html__( 'IP', 'knife-theme' ),
            'status'   => esc_html__( 'Статус', 'knife-theme' ),
        );

        return $columns;
    }

    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $columns = array(
            'created'  => array(
                'created',
                true,
            ),

            'received' => array(
                'received',
                true,
            ),

            'clicks'   => array(
                'clicks',
                true,
            ),

            'opens'    => array(
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
            'active'       => esc_html__( 'Активировать', 'knife-theme' ),
            'unsubscribed' => esc_html__( 'Отписать', 'knife-theme' ),
            'block'        => esc_html__( 'Заблокировать', 'knife-theme' ),
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
            'orderby'     => 'created',
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
            $search_query = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );

            $args['where'] = $db->prepare(
                'users.email LIKE %s',
                '%' . $db->esc_like( $search_query ) . '%'
            );
        }

        $query = 'SELECT users.id AS id,
            users.email AS email,
            users.status AS status,
            users.ip AS ip,
            users.created AS created,
            IFNULL(SUM(action = "received"), 0) received,
            IFNULL(SUM(action = "click"), 0) clicks,
            IFNULL(SUM(action = "open"), 0) opens
            FROM users
            LEFT JOIN actions ON users.id = actions.users_id
            WHERE %s
            GROUP BY users.id
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
