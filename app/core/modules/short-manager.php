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
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add managment menu page
        add_action('admin_menu', [__CLASS__, 'add_management_page'], 20);

        // Init second database connection on proper page
        add_action('current_screen', [__CLASS__, 'connect_short_db']);

        // Append short link if necessary
        add_action('current_screen', [__CLASS__, 'process_short_link']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Screen options
        add_action('load-tools_page_' . self::$page_slug, function() {
            $args = array(
                'option' => str_replace( 'tools_page_', '', get_current_screen()->id ) . '_requests_per_page',
            );

            add_screen_option( 'per_page', $args );
        });
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
     * Display management page
     */
    public static function display_management_page() {
        $include = get_template_directory() . '/core/include';

        // Include Short Links table class
        include_once($include . '/tables/short-links.php');

        // Get short links table instance
        $table = new Knife_Short_Links_Table(self::$short_db);

        // Include options template
        include_once($include . '/templates/short-options.php');
    }


    /**
    * Enqueue assets to admin certain screen only
    */
    public static function enqueue_assets($hook) {
        $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style('knife-short-options', $include . '/styles/short-options.css', [], $version);
    }


    /**
     * Create custom database connection
     */
    public static function connect_short_db() {
        $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

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
    public static function process_short_link() {
        $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

        $action = isset($_POST['action']) ? sanitize_key($_POST['action']) : '';

        if('knife-short-append' === $action) {
            self::append_short_link();
        }
    }


    /**
     * Append short link
     */
    private function append_short_link() {
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

            if($data['keyword'] === null) {
                return add_settings_error('knife-short-actions', 'knife-short-append',
                    __('Короткий адрес уже существует', 'knife-theme')
                );
            }

            $data['title'] = self::get_link_title(
                esc_url_raw($_POST['url'])
            );

            if($db->insert('urls', $data)) {
                return add_settings_error('knife-short-actions', 'knife-short-append',
                    __('Ссылка успешно добавлена', 'knife-theme'), 'updated'
                );
            }
        }

        return add_settings_error('knife-short-actions', 'knife-short-append',
            __('Не удалось добавить ссылку', 'knife-theme')
        );
    }


    /**
     * Check keyword existance
     */
    private static function get_link_keyword($keyword) {
        $keyword = str_replace('_', '-', substr($keyword, 0, 200));

        // Make verification request
        $query = $db->prepare('SELECT id FROM urls WHERE keyword = %s', $keyword);

        if($db->get_var($query) === null) {
            return false;
        }

        return $keyword;
    }


    /**
     * Get page title
     *
     * Return post title if the url from this site
     * Else try to fecth page title using remote api
     */
    private static function get_link_title($url) {
        $post_id = url_to_postid($url);

        if($post_id > 0 ) {
            return esc_html(get_the_title($post_id));
        }

        $response = wp_safe_remote_get($url, [
            'timeout' => 3
        ]);

        $content = wp_remote_retrieve_body($response);

        // Find page title in the content
        if(preg_match('~<title[^>]*>(.+?)</title>~iU', $content, $title)) {
            return esc_html(trim($title[1]));
        }

        return $url;
    }
}


/**
 * Load current module environment
 */
Knife_Short_Manager::load_module();
