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
            '<a href="%s" class="row-title" target="_blank">%s</a>',
            esc_url(get_permalink($item['post_id'])),
            esc_html(get_the_title($item['post_id']))
        );

        return $markup;
    }


    /**
     * Fix timestamp format
     */
    public function column_publish($item) {
        return get_the_date('d.m.Y', $item['post_id']);
    }


    /**
     * Get columns to show in the list table.
     */
    public function get_columns() {
        $columns = [
            'title' => __('Заголовок', 'knife-theme'),
            'pageviews' => __('Просмотры', 'knife-theme'),
            'uniqueviews' => __('Уникальные просмотры', 'knife-theme'),
            'publish' => __('Дата публикации', 'knife-theme')
        ];

        return $columns;
    }


    /**
     * Return columns that may be used to sort table
     */
    public function get_sortable_columns() {
        $columns = [];

        return $columns;
    }


    /**
     * Get rows from database and prepare them to be showed in table
     */
    public function prepare_items() {
        $this->_column_headers = [
            $this->get_columns(), [], $this->get_sortable_columns()
        ];

        $db = $this->views_db;

        $args = [
            'orderby' => 'date',
            'order' => 'desc',
            'post_type' => 'any',
            'paged' => 0,
            'posts_per_page' => 20,
            'fields' => 'ids'
        ];

        $args['per_page'] = $this->get_items_per_page($this->per_page);

        if(isset($_REQUEST['paged'])) {
            $args['paged'] = max(0, intval($_REQUEST['paged'] - 1) * $args['per_page']);
        }

        $query = new WP_Query($args);

        // Glue returned posts
        $posts = implode(',', $query->posts);

        // Create select query
        $views = $db->get_results("SELECT * FROM posts WHERE post_id IN ({$posts})", ARRAY_A);

        // Get array coumn by post_id
        $pluck = wp_list_pluck($views, 'post_id');

        foreach($query->posts as $id) {
            $item = [
                'post_id' => $id,
                'pageviews' => 0,
                'uniqueviews' => 0
            ];

            $key = array_search($id, $pluck);

            if($key !== false) {
                $item = wp_parse_args($views[$key], $item);
            }

            // Add to items
            $this->items[] = $item;
        }


        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page' => $args['posts_per_page']
        ]);
    }
}
