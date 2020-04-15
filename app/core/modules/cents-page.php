<?php
/**
 * Cents page
 *
 * Display short tips and stories from Telegram channel
 *
 * @package knife-theme
 * @since 1.12
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Cents_Page {
    /**
     * Store cents posts from database
     *
     * @access  private
     * @var     array
     */
    private static $cents_cards = [];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Try to load data from remote db
        add_action('wp', [__CLASS__, 'load_cents']);

        // Include template for cents posts
        add_filter('template_include', [__CLASS__, 'include_template']);


        // Include posts to page
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Define cents settings if still not
        if(!defined('KNIFE_CENTS')) {
            define('KNIFE_CENTS', []);
        }
    }


    /**
     * Include template for cents posts
     */
    public static function include_template($template) {
        if(empty(KNIFE_CENTS['page']) || !is_page(KNIFE_CENTS['page'])) {
            return $template;
        }

        $new_template = locate_template(['templates/single-cents.php']);

        if(empty($new_template)) {
            $new_template = $template;
        }

        return $new_template;
    }


    /**
     * Try to load data from remote db
     */
    public static function load_cents() {
        global $wp_query;

        if(empty(KNIFE_CENTS['page']) || !is_page(KNIFE_CENTS['page'])) {
            return;
        }

        // Mix with default values
        $conf = wp_parse_args(KNIFE_CENTS, [
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

        // Get cards from remote database
        $query = "SELECT title, content, source, link FROM messages ORDER BY created DESC";
        $cards = $db->get_results($query, ARRAY_A);

        if(count($cards) === 0) {
            $wp_query->set_404();

            status_header(404);
            nocache_headers();
        }

        self::$cents_cards = $cards;
    }


    /**
     * Include cents posts
     */
    public static function inject_object() {
        $cards = self::$cents_cards;

        if(count($cards) === 0) {
            return;
        }

        // Add cents posts
        wp_localize_script('knife-theme', 'knife_cents_cards', self::$cents_cards);

        $options = [
            'label' => __('Источник', 'knife-theme')
        ];

        // Add cents options
        wp_localize_script('knife-theme', 'knife_cents_options', $options);
    }
}


/**
 * Load current module environment
 */
Knife_Cents_Page::load_module();
