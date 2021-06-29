<?php
/**
 * Analytics manager table class
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.15
 */

if (!defined('WPINC')) {
    die;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Knife_Analytics_Managers_Table extends WP_List_Table {
    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private $analytics_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private $per_page = '';

    /**
     * The current post filter, if is used (depending on $_GET['post_type_filter'])
     *
     * @access private
     * @var    string
     */
    private $cat_filter = null;


    /**
     * Analytics Manager table constructor
     *
     * Uses analytics link database wpdb instance
     */
    public function __construct($db, $per_page) {
        parent::__construct([
            'plural' => 'pageviews',
            'ajax' => false
        ]);

        if(isset($_GET['cat_id'])) {
            $this->cat_filter = absint($_GET['cat_id']);
        }

        $this->analytics_db = $db;
        $this->per_page = $per_page;
    }


    /**
     * Add table filter block
     */
    public function extra_tablenav($which) {
        if('top' !== $which) {
            return;
        }

        $options = array(
            'selected' => $this->cat_filter,
            'name' => 'cat_id',
            'taxonomy' => 'category',
            'show_option_all' => get_taxonomy('category')->labels->all_items,
            'hide_empty' => true,
            'hierarchical' => false,
            'show_count' => false,
            'echo' => false,
        );

        printf(
            '<div class="alignleft actions">%s</div>',
            wp_dropdown_categories($options)
        );

        submit_button(__('Фильтр', 'knife-theme'), '', null, false);
    }


    /**
     * Default column render
     */
    public function column_default($item, $column_name) {
        if(isset($item[$column_name])) {
            return $item[$column_name];
        }

        return null;
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
            'vk' => __('ВКонтакте', 'knife-theme'),
            'fb' => __('Facebook', 'knife-theme'),
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

        $db = $this->analytics_db;

        $args = [
            'orderby' => 'date',
            'order' => 'desc',
            'post_type' => 'any',
            'post_status' => 'publish',
            'paged' => 0,
            'posts_per_page' => 20,
            'cat' => $this->cat_filter,
            'fields' => 'ids'
        ];

        $args['per_page'] = $this->get_items_per_page($this->per_page);

        if(isset($_REQUEST['paged'])) {
            $args['paged'] = intval($_REQUEST['paged']);
        }

        $query = new WP_Query($args);

        // Glue returned posts
        $posts = implode(',', $query->posts);

        // Create select query
        $analytics = $db->get_results(
            "SELECT post_id, slug, pageviews, publish, fb, vk FROM posts
            LEFT JOIN shares USING (post_id)
            LEFT JOIN views USING (post_id)
            WHERE post_id IN ({$posts})",
        ARRAY_A);

        // Get array coumn by post_id
        $pluck = wp_list_pluck($analytics, 'post_id');

        foreach($query->posts as $id) {
            $item = [
                'post_id' => $id,
                'pageviews' => 0,
            ];

            $key = array_search($id, $pluck);

            if($key !== false) {
                $item = wp_parse_args($analytics[$key], $item);
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
