<?php
/**
 * News manager
 *
 * Manage news category
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.7
 */

if (!defined('WPINC')) {
    die;
}

class Knife_News_Manager {
    /**
     * Unique slug using for news category url
     *
     * @access  private
     * @var     string
     */
    private static $news_slug = 'news';

    /**
     * News category id
     *
     * @access  private
     * @var     int
     */
    private static $news_id = null;


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include news archive template
        add_filter('archive_template', [__CLASS__, 'include_archive']);

        // Remove news from home page
        add_action('pre_get_posts', [__CLASS__, 'remove_home']);

        // Change posts count on news archive
        add_action('pre_get_posts', [__CLASS__, 'update_count']);

        // Set custom categories dropdown
        add_filter('disable_categories_dropdown', '__return_true');
        add_action('restrict_manage_posts', [__CLASS__, 'print_dropdown']);
        add_action('parse_query', [__CLASS__, 'process_dropdown']);

        // Update related posts in sidebar
        add_action('pre_get_posts', [__CLASS__, 'update_related']);

        // Set news_id
        $category = get_category_by_slug('news');

        if(isset($category->term_id)) {
            self::$news_id = $category->term_id;
        }
    }


    /**
     * Include custom archive template for news
     */
    public static function include_archive($template) {
        if(is_category(self::$news_id)) {
            $new_template = locate_template(['templates/archive-news.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Update related posts in sidebar
     *
     * @since 1.5
     */
    public static function update_related($query) {
        if($query->get('query_type') === 'related') {
            $query->set('category__not_in', [self::$news_id]);

            if(in_category(self::$news_slug)) {
                $query->set('post__in', [0]);
            }
        }
    }


    /**
     * Remove news from home page
     */
    public static function remove_home($query) {
        if($query->is_main_query() && $query->is_home()) {
            $query->set('category__not_in', [self::$news_id]);
        }
    }


    /**
     * Change posts_per_page for news category archive template
     */
    public static function update_count($query) {
        if($query->is_main_query() && $query->is_category(self::$news_slug)) {
            $query->set('posts_per_page', 20);
        }
    }


    /**
     * Print custom categories dropdown
     */
    public static function print_dropdown($post_type) {
        if($post_type === 'post') {
            $values = [
                __('Все записи', 'knife-theme') => 0,
                __('Только новости', 'knife-theme') => self::$news_slug,
                __('Без новостей', 'knife-theme') => 'other'
            ];

            $current = $_GET['cat'] ?? '';

            print '<select name="cat">';

            foreach($values as $label => $value) {
                printf('<option value="%s"%s>%s</option>', $value, $value == $current ? ' selected="selected"' : '', $label);
            }

            print '</select>';
        }
    }


    /**
     * Process custom categories dropdown
     */
    public static function process_dropdown($query) {
        global $pagenow;

        if(!is_admin() || $pagenow !== 'edit.php' || empty($_GET['cat'])) {
            return false;
        }

        if(isset($_GET['post_type']) && $_GET['post_type'] !== 'post') {
            return false;
        }

        $classics = get_category_by_slug('classics');

        if($_GET['cat'] === self::$news_slug) {
            $query->query_vars['cat'] = self::$news_id;
        }

        if($_GET['cat'] === 'other') {
            $query->query_vars['category__not_in'] = [self::$news_id, $classics->term_id];
        }
    }
}


/**
 * Load current module environment
 */
Knife_News_Manager::load_module();
