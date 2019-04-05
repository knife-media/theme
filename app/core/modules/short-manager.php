<?php
/**
 * Short manager
 *
 * Return short links manager with simple stat
 *
 * @package knife-theme
 * @since 1.8
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Short_Manager {
    /**
     * Management page slug
     *
     * @access  private
     * @var     string
     */
    private static $page_slug = 'knife-short';


    /**
     * Store tools page base_id screen
     *
     * @access  private
     * @var     string
     */
    private static $screen_base = null;


    /**
     * Short link database wpdb instance
     *
     * @access  private
     * @var     object
     */
    private static $short_db = null;


    /**
     * Option name to store table per_page option
     *
     * @access  private
     * @var     string
     */
    private static $per_page = 'knife_short_per_page';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action('admin_menu', [__CLASS__, 'add_management_page'], 20);

        // Init short links action before page load
        add_action('current_screen', [__CLASS__, 'init_short_actions']);

        // Save links per page screen option
        add_filter('set-screen-option', [__CLASS__, 'save_screen_options'], 10, 3);
    }


    /**
     * Add short links management page
     */
    public static function add_management_page() {
        $hookname = add_management_page(
            __('Сокращатель ссылок', 'knife-theme'),
            __('Сокращатель ссылок', 'knife-theme'),
            'publish_pages', self::$page_slug,
            [__CLASS__, 'display_management_page']
        );

        // Set tools page base_id screen
        self::$screen_base = $hookname;
    }


    /**
     * Short links actions
     */
    public static function init_short_actions() {
        $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

        if(!current_user_can('publish_pages')) {
            wp_die(__('Извините, у вас нет доступа к этому инструменту', 'knife-theme'));
        }

        // Init second database connection
        self::connect_short_db();

        // Append short link if necessary
        self::process_short_link();

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Add screen options
        add_action('load-' . self::$screen_base, [__CLASS__, 'add_screen_options']);
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
        if(!current_user_can('publish_pages')) {
            wp_die(__('Извините, у вас нет доступа к этому инструменту', 'knife-theme'));
        }

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
     * Enqueue assets to admin certain screen only
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style('knife-short-options', $include . '/styles/short-options.css', [], $version);
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
        if(!defined('WP_SHORT_MANAGER')) {
            define('WP_SHORT_MANAGER', []);
        }

        // Mix with custom module settings
        $settings = wp_parse_args(WP_SHORT_MANAGER, [
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD
        ]);

        extract($settings);

        $db = new wpdb($user, $password, $name, $host);

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

        return add_settings_error('knife-short-actions', 'append',
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
        if(function_exists('mb_strlen') && mb_strlen($title) > 100) {
            $title = mb_substr($title, 0, 100) . '…';
        }

        return $title;
    }
}


/**
 * Load current module environment
 */
Knife_Short_Manager::load_module();
