<?php
/**
 * Views manager table class
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Knife_Views_Managers_Table extends WP_List_Table {
    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private $views_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private $per_page = '';


    /**
     * Views Manager table constructor
     *
     * Uses views link database wpdb instance
     */
    public function __construct($db, $per_page) {
        parent::__construct([
            'plural' => 'pageviews',
            'ajax' => false
        ]);

        $this->views_db = $db;
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
            esc_url(get_permalink($item['ID'])),
            esc_html(get_the_title($item['ID']))
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
    public function column_publish($item) {
        $publish = strtotime($item['publish']);
        $current = current_time('publish');

        if($current >= $publish && $publish - $current < DAY_IN_SECONDS) {
            return sprintf(__('%s назад', 'knife-theme'), human_time_diff($current, $publish));
        }

        return date_i18n(get_option('date_format'), $publish);
    }


    /**
     * Get columns to show in the list table.
     */
    public function get_columns() {
        $columns = [
            'title' => __('Название', 'knife-theme'),
            'pageviews' => __('Короткий адрес', 'knife-theme'),
            'uniqueviews' => __('Дата', 'knife-theme'),
            'publish' => __('Переходы', 'knife-theme')
        ];

        return $columns;
    }


    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $columns = [
            'pageviews' => [
                'pageviews', true
            ],

            'uniqueviews' => [
                'uniqueviews', true
            ],

            'publish' => [
                'publish', true
            ]
        ];

        return $columns;
    }


    /**
     * Get rows from database and prepare them to be showed in table
     */
    public function prepare_items() {
        global $wpdb;

        $this->_column_headers = [
            $this->get_columns(), [], $this->get_sortable_columns()
        ];

        $db = $wpdb;

        $args = [
            'orderby' => 'post_date',
            'order' => 'desc',
            'paged' => 0,
            'where' => "post_status = 'publish'",
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
            if(in_array(strtoupper($_REQUEST['order']), array('ASC', 'DESC'), true)) {
                $args['order'] = strtoupper($_REQUEST['order']);
            }
        }

        if(isset($_REQUEST['s']) && strlen($_REQUEST['s']) > 0) {
            $args['where'] = $db->prepare("CONCAT(keyword, url, title) LIKE %s",
                '%' . $db->esc_like($_REQUEST['s']) . '%'
            );
        }

        $query = sprintf(
            "SELECT SQL_CALC_FOUND_ROWS * FROM wp_posts WHERE %s ORDER BY %s %s LIMIT %d OFFSET %d",
            $args['where'], $args['orderby'], $args['order'], $args['per_page'], $args['paged']
        );

        $this->items = $db->get_results($query, ARRAY_A);

        if(!$db->last_error) {
            $args['total_items'] = $db->get_var("SELECT FOUND_ROWS()");
        }

        $this->set_pagination_args(array(
            'total_items' => $args['total_items'],
            'per_page' => $args['per_page']
        ));
    }
}
