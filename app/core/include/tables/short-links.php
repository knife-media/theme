<?php
/**
 * Short links table class
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.12
 */

if (!defined('WPINC')) {
    die;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Knife_Short_Links_Table extends WP_List_Table {
    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private $short_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private $per_page = '';


    /**
     * Short Links table constructor
     *
     * Uses short link database wpdb instance
     */
    public function __construct($db, $per_page) {
        parent::__construct([
            'plural' => 'shortlinks',
            'ajax' => false
        ]);

        $this->short_db = $db;
        $this->per_page = $per_page;
    }


    /**
     * Default column render
     */
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }


    /**
     * Title column render
     */
    public function column_title($item) {
        $markup = sprintf(
            '<a href="%1$s" class="row-title" target="_blank">%2$s</a><em>%1$s</em>',
            esc_html($item['url']),
            esc_html($item['title'])
        );

        return $markup;
    }


    /**
     * Keyword column render
     */
    public function column_keyword($item) {
        $conf = KNIFE_SHORT;

        if(empty($conf['url'])) {
            return $item['keyword'];
        }

        $link = trailingslashit($conf['url']) . $item['keyword'];

        $markup = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            esc_url(trailingslashit($link)),
            esc_html($item['keyword'])
        );

        return $markup;
    }


    /**
     * Fix timestamp format
     */
    public function column_timestamp($item) {
        $publish = strtotime($item['timestamp']);
        $current = current_time('timestamp');

        if($current >= $publish && $publish - $current < DAY_IN_SECONDS) {
            return sprintf(__('%s назад', 'knife-theme'), human_time_diff($current, $publish));
        }

        return date_i18n(get_option('date_format'), $publish);
    }


    /**
     * Checkbox column render
     */
    public function column_cb($item) {
        $markup = sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            absint($item['id'])
        );

        return $markup;
    }


    /**
     * Get columns to show in the list table.
     */
    public function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'title' => __('Название', 'knife-theme'),
            'keyword' => __('Короткий адрес', 'knife-theme'),
            'timestamp' => __('Дата', 'knife-theme'),
            'clicks' => __('Переходы', 'knife-theme')
        ];

        return $columns;
    }


    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $columns = [
            'title' => [
                'title', true
            ],

            'timestamp' => [
                'timestamp', true
            ],

            'clicks' => [
                'clicks', true
            ]
        ];

        return $columns;
    }


    /**
     * Return array of bulk actions
     */
    public function get_bulk_actions() {
        $actions = [
            'delete' => __('Удалить', 'knife-theme')
        ];

        return $actions;
    }


    /**
     * Process bulk actions
     */
    public function process_actions() {
        $ids = [];

        if('delete' === $this->current_action()) {
            check_admin_referer('bulk-' . $this->_args['plural']);

            if(isset($_REQUEST['id'])) {
                $ids = wp_parse_id_list(wp_unslash($_REQUEST['id']));
            }

            $db = $this->short_db;

            if(count($ids) > 0) {
                $ids = implode(',', $ids);

                // Delete given ids
                if($db->query("DELETE FROM urls WHERE id IN({$ids})")) {
                    add_settings_error('knife-short-actions', 'remove',
                        __('Ссылки успешено удалены', 'knife-theme'), 'updated'
                    );
                }
            }
        }
    }


    /**
     * Get rows from database and prepare them to be showed in table
     */
    public function prepare_items() {
        $this->_column_headers = [
            $this->get_columns(), [], $this->get_sortable_columns()
        ];

        $db = $this->short_db;

        $args = [
            'orderby' => 'timestamp',
            'order' => 'desc',
            'paged' => 0,
            'where' => '1=1',
            'per_page' => 20,
            'total_items' => 0,
        ];

        $args['per_page'] = $this->get_items_per_page($this->per_page);

        if(isset($_REQUEST['paged'])) {
            $args['paged'] = max(0, intval($_REQUEST['paged'] - 1) * $args['per_page']);
        }

        if(isset($_REQUEST['orderby'])) {
            if(array_key_exists($_REQUEST['orderby'], $this->get_sortable_columns())) {
                $args['orderby'] = $_REQUEST['orderby'];
            }
        }

        if(isset($_REQUEST['order'])) {
            if(in_array(strtoupper($_REQUEST['order']), ['ASC', 'DESC'], true)) {
                $args['order'] = strtoupper($_REQUEST['order']);
            }
        }

        if(isset($_REQUEST['s']) && strlen($_REQUEST['s']) > 0) {
            $args['where'] = $db->prepare("CONCAT(keyword, url, title) LIKE %s",
                '%' . $db->esc_like($_REQUEST['s']) . '%'
            );
        }

        $query = sprintf(
            "SELECT SQL_CALC_FOUND_ROWS * FROM urls WHERE %s ORDER BY %s %s LIMIT %d OFFSET %d",
            $args['where'], $args['orderby'], $args['order'], $args['per_page'], $args['paged']
        );

        $this->items = $db->get_results($query, ARRAY_A);

        if(!$db->last_error) {
            $args['total_items'] = $db->get_var("SELECT FOUND_ROWS()");
        }

        $this->set_pagination_args([
            'total_items' => $args['total_items'],
            'per_page' => $args['per_page']
        ]);
    }
}
