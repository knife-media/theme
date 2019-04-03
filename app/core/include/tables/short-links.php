<?php
/**
 * Short links table class
 *
 * @package knife-theme
 * @since 1.8
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
     * Short Links table constructor
     *
     * Uses short link database wpdb instance
     */
    public function __construct($db) {
        // Init parent construct
        parent::__construct([
            'singular' => 'link',
            'plural' => 'links'
        ]);

        // Add screen option
        add_screen_option('per_page', [
            'label' => __('Показывать на странице', 'knife-theme'),
            'default' => 20,
            'option' => 'links_per_page',
        ]);

        // Set db instance
        $this->short_db = $db;

        // Prepare table items to display
        $this->prepare_items();
    }


    /**
     * Default column render
     */
    public function column_default($item, $column_name) {
        return $item[$column_name];
    }


    /**
     * Keyword column render
     */
    public function column_title($item) {
        $actions = [
            'view' => sprintf('<a href="%s" target="_blank">%s</a>',
                esc_url($item['url']),
                __('Открыть ссылку в новом окне', 'knife-theme')
            ),
        ];

        $markup = sprintf('<strong class="row-title">%s</strong>%s',
            esc_html($item['title']),
            $this->row_actions($actions)
        );

        return $markup;
    }


    /**
     * Fix timestamp format
     */
    public function column_timestamp($item) {
        $timestamp = strtotime($item['timestamp']);

        // Get time diff
        $time_diff = current_time('timestamp', true) - $timestamp;

        if($time_diff >= 0 && $time_diff < DAY_IN_SECONDS) {
            return sprintf(__('%s назад', 'knife-theme'), human_time_diff($timestamp));
        }

        return date_i18n(get_option('date_format'), $timestamp);
    }


    /**
     * Make url clickable
     */
    public function column_url($item) {
        $markup = sprintf('<a href="%1$s" target="_blank">%1$s</a>',
            esc_url($item['url'])
        );

        return $markup;
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
            'url' => __('Полная ссылка', 'knife-theme'),
            'timestamp' => __('Дата', 'knife-theme'),
            'clicks' => __('Переходы', 'knife-theme'),
        ];

        return $columns;
    }


    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $sortable_columns = [
            'title' => ['title', true],
            'timestamp' => ['timestamp', true],
            'clicks' => ['clicks', true]
        ];

        return $sortable_columns;
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
    public function process_actions($db) {
        $ids = [];

        if('delete' === $this->current_action()) {
            check_admin_referer('bulk-' . $this->_args['plural']);

            if(isset($_REQUEST['id'])) {
                $ids = wp_parse_id_list(wp_unslash($_REQUEST['id']));
            }

            if(count($ids) > 0) {
                $ids = implode(',', $ids);

                // Delete given ids
                if($db->query("DELETE FROM urls WHERE id IN({$ids})")) {
                    add_settings_error('knife-short-action', 'knife-short-action',
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
        $db = $this->short_db;

        // Process actions first
        $this->process_actions($db);

        $table_name = 'urls';
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();





        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);
        // [OPTIONAL] process bulk action if any
        // will be used in pagination settings
        $total_items = $db->get_var("SELECT COUNT(keyword) FROM $table_name");
        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'keyword';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $db->get_results($db->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
