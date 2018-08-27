<?php
/**
* News manager
*
* Manage news category
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

(new Knife_News_Manager)->init();

class Knife_News_Manager {
    /**
     * Unique slug using for news category url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'news';

    /**
     * News category id
     *
     * @since   1.3
     * @access  private
     * @var     int
     */
    private $news_id = 620;


    /**
     * Init function instead of constructor
     *
     * @since 1.3
     */
    public function init() {
        // Remove news from home page
        add_action('pre_get_posts', [$this, 'remove_home']);

        // Change posts count on news archive
        add_action('pre_get_posts', [$this, 'update_count']);

        // Set custom categories dropdown
        add_filter('disable_categories_dropdown', '__return_true', 'post');
        add_action('restrict_manage_posts', [$this, 'print_dropdown'], 'post');
        add_action('parse_query', [$this, 'process_dropdown']);

        // Apply theme hooks
        add_action('after_setup_theme', [$this, 'setup_theme']);
    }


    /**
     * Setup theme hooks
     */
    public function setup_theme() {
        // News archive template
        add_filter('knife_template_archive', function($template) {
            if(is_category($this->news_id)) {
                $template = $this->slug;
            }

            return $template;
        });

        // News archive header
        add_filter('knife_archive_header', function($header) {
            if(is_category($this->news_id)) {
                $header = '';
            }

            return $header;
        });

        // Remove promo from news content
        add_filter('the_content', function($content) {
            if(in_category($this->news_id)) {
                remove_filter('the_content', ['Knife_User_Club', 'insert_post_promo']);
            }

            return $content;
        }, 9);
    }


    /**
     * Remove news from home page
     */
    public function remove_home($query) {
        if($query->is_main_query() && $query->is_home()) {
            $query->set('category__not_in', [$this->news_id]);
        }
    }


    /**
     * Change posts_per_page for news category archive template
     */
    public function update_count($query) {
        if($query->is_main_query() && $query->is_category($this->slug)) {
            $query->set('posts_per_page', 20);
        }
    }


    /**
     * Print custom categories dropdown
     */
    public function print_dropdown() {
        $values = [
            __('Все записи', 'knife-theme') => 0,
            __('Только новости', 'knife-theme') => $this->slug,
            __('Без новостей', 'knife-theme') => 'other'
        ];

        $current = $_GET['cat'] ?? '';

        print '<select name="cat">';

        foreach($values as $label => $value) {
            printf('<option value="%s"%s>%s</option>', $value, $value == $current ? ' selected="selected"' : '', $label);
        }

        print '</select>';
    }


    /**
     * Process custom categories dropdown
     */
    public function process_dropdown($query) {
        global $pagenow;

        if(!is_admin() || $pagenow !== 'edit.php' || empty($_GET['cat'])) {
            return false;
        }

        if(isset($_GET['post_type']) && $_GET['post_type'] !== 'post') {
            return false;
        }

        $classics = get_category_by_slug('classics');

        if($_GET['cat'] === $this->slug) {
            $query->query_vars['cat'] = $this->news_id;
        }

        if($_GET['cat'] === 'other') {
            $query->query_vars['category__not_in'] = [$this->news_id, $classics->term_id];
        }
    }
}
