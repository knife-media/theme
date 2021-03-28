<?php
/**
 * Google custom search engine
 *
 * Set google custom search app id and posts count settings
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.15
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Google_Search {
    /**
     * Search page query var
     *
     * @access  public
     * @var     string
     * @since   1.12
     */
    public static $query_var = 'search';


    /**
     * Init function instead of constructor
     *
     * @since 1.4
     */
    public static function load_module() {
        // Include Google Custom Search js sdk
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Disable default search
        add_action('parse_query', [__CLASS__, 'disable_search'], 9);

        // Update query virtual page
        add_action('parse_query', [__CLASS__, 'update_query_vars']);

        // Create custom search page rewrite url
        add_action('init', [__CLASS__, 'add_search_rule']);

        // Add search query tag
        add_action('query_vars', [__CLASS__, 'append_search_var']);

        // Include archive template for search results
        add_filter('template_include', [__CLASS__, 'include_search']);

        // Update search results archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_document_title']);

        // Set is-gcse class if need
        add_filter('body_class', [__CLASS__, 'set_body_class'], 11);

        // Define google search settings if still not
        if(!defined('KNIFE_GCSE')) {
            define('KNIFE_GCSE', []);
        }
    }

    /**
     * Create custom search rule
     *
     * @since 1.12
     */
    public static function add_search_rule() {
        add_rewrite_rule(
            sprintf('^%s/?$', self::$query_var),
            sprintf('index.php?%s=1', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/[^/]+/(?:feed/)?', self::$query_var),
            sprintf('index.php', self::$query_var),
            'top'
        );
    }


    /**
     * Disable wordpress based search to reduce CPU load and prevent DDOS attacks
     */
    public static function disable_search($query) {
        if(!is_admin() && $query->is_search()) {
            $query->set('s', '');
            $query->is_search = false;
            $query->is_404 = true;
        }
    }


    /**
     * Update query_vars for search archive
     *
     * @since 1.12
     */
    public static function update_query_vars($query) {
        if(array_key_exists(self::$query_var, $query->query_vars)) {
            $query->is_archive = true;
            $query->is_home = false;
        }
    }


    /**
     * Append search query tag to availible query vars
     *
     * @since 1.12
     */
    public static function append_search_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include template for search results
     *
     * @since 1.12
     */
    public static function include_search($template) {
        if(get_query_var(self::$query_var)) {
            $new_template = locate_template(['search.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Include app id for Google Custom Search API to knife-theme js script
     */
    public static function inject_object() {
        if(empty(KNIFE_GCSE['id'])) {
            return;
        }

        $options = [
            'id' => KNIFE_GCSE['id'],
            'placeholder' => __('Введите фразу для поиска', 'knife-theme')
        ];

        wp_localize_script('knife-theme', 'knife_search_options', $options);
    }


    /**
     * Update search archive document title
     *
     * @since 1.12
     */
    public static function update_document_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Поиск по материалам', 'knife-theme');
        }

        return $title;
    }


    /**
     * Set is-gcse body class
     */
    public static function set_body_class($classes = []) {
        if(get_query_var(self::$query_var)) {
            $classes[] = 'is-search';
        }

        return $classes;
    }
}


/**
 * Load current module environment
 */
Knife_Google_Search::load_module();
