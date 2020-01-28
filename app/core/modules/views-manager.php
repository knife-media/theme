<?php
/**
 * Views manager
 *
 * Collect views from Google Analytics
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Analytics_Manager {
    /**
     * Management page slug
     *
     * @access  public
     * @var     string
     */
    public static $page_slug = 'knife-analytics';


    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private static $analytics_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private static $per_page = 'knife_analytics_per_page';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
#        add_action('admin_menu', [__CLASS__, 'add_management_page'], 20);

        // Save links per page screen option
#        add_filter('set-screen-option', [__CLASS__, 'save_screen_options'], 10, 3);

        // Define short links settings if still not
        if(!defined('KNIFE_ANALYTICS')) {
            define('KNIFE_ANALYTICS', []);
        }
    }


    /**
     * Add short links management page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            __('Сокращатель ссылок', 'knife-theme'),
            __('Сокращатель ссылок', 'knife-theme'),
            'unfiltered_html', self::$page_slug,
            [__CLASS__, 'display_management_page']
        );
    }


    /**
     * Save screen options
     * This function should fire earlier than admin_menu hook
     */
    public static function save_screen_options($status, $option, $value) {
        if($option === self::$per_page) {
            return absint($value);
        }

        return $status;
    }


    /**
     * Display management page
     */
    public static function display_management_page() {
        $include = get_template_directory() . '/core/include';

        // Include Short Links table class
        include_once($include . '/tables/short-links.php');

        // Get short links table instance
        $table = new Knife_Short_Links_Table(self::$short_db, self::$per_page);

        $table->process_actions();
        $table->prepare_items();

        // Include options template to show table
        include_once($include . '/templates/short-options.php');
    }


    /**
     * Add tools page screen options
     */
    public static function add_screen_options() {
        add_screen_option('per_page', [
            'option' => self::$per_page
        ]);
    }


    /**
     * Create custom database connection
     */
    private static function connect_short_db() {
        // Mix with default values
        $conf = wp_parse_args(KNIFE_SHORT, [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD
        ]);

        // Create custom db connection
        $db = new wpdb($conf['user'], $conf['password'], $conf['name'], $conf['host']);
        $db->hide_errors();

        if(isset($db->error)) {
            wp_die($db->error);
        }

        self::$short_db = $db;
    }


    /**
     * Short links actions handler
     */
    private static function process_short_link() {
        $action = isset($_POST['action']) ? sanitize_key($_POST['action']) : '';

        if($action === self::$page_slug . '-append') {
            self::append_short_link();
        }
    }


    /**
     * Append short link
     */
    private static function append_short_link() {
        check_admin_referer('knife-short-append');

        $db = self::$short_db;

        // Check if required values not empty
        if(!empty($_POST['keyword']) && !empty($_POST['url'])) {
            $data = [
                'url' => $_POST['url'],
                'ip' => $_SERVER['REMOTE_ADDR']
            ];

            $data['keyword'] = self::get_link_keyword(
                sanitize_key($_POST['keyword'])
            );

            if($data['keyword'] === false) {
                return add_settings_error('knife-short-actions', 'append',
                    __('Короткий адрес уже существует', 'knife-theme')
                );
            }

            $data['title'] = self::get_link_title(
                sanitize_text_field($_POST['url'])
            );

            if($db->insert('urls', $data)) {
                return add_settings_error('knife-short-actions', 'append',
                    __('Ссылка успешно добавлена', 'knife-theme'), 'updated'
                );
            }
        }

        add_settings_error('knife-short-actions', 'append',
            __('Не удалось добавить ссылку', 'knife-theme')
        );
    }


    /**
     * Check keyword existance
     */
    private static function get_link_keyword($keyword) {
        $db = self::$short_db;

        // Cut and replace dashes
        $keyword = str_replace('_', '-', substr($keyword, 0, 200));

        // Make verification request
        $query = $db->prepare('SELECT id FROM urls WHERE keyword = %s', $keyword);

        if($db->get_var($query) === null) {
            return $keyword;
        }

        return false;
    }


    /**
     * Get page title
     *
     * Return post title if the url from this site
     * Else try to fecth page title using remote api
     */
    private static function get_link_title($url) {
        $post_id = url_to_postid($url);

        if($post_id > 0) {
            return esc_html(get_the_title($post_id));
        }

        $response = wp_safe_remote_get(esc_url_raw($url), [
            'timeout' => 3
        ]);

        $title = $url;

        // Try to fetch page content by url
        if(wp_remote_retrieve_response_code($response) === 200) {
            $content = wp_remote_retrieve_body($response);

            // Find page title in the content
            if(preg_match('~<title[^>]*>(.+?)</title>~iU', $content, $string)) {
                $title = esc_html(trim($string[1]));
            }
        }

        // Cut title if too long
        if(mb_strlen($title) > 100) {
            $title = mb_substr($title, 0, 100) . '…';
        }

        return $title;
    }
}


/**
 * Load current module environment
 */
Knife_Analytics_Manager::load_module();
