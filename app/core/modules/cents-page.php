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
     * Post meta to store cents posts
     *
     * @access  public
     * @var     string
     */
    public static $meta_cents = '_knife-cents';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Schedule event to update from remote db
        add_action('wp', [__CLASS__, 'schedule_sync']);

        // Add schedule interval
        add_filter('cron_schedules', [__CLASS__, 'add_cron_interval']);

        // Include template for cents posts
        add_filter('template_include', [__CLASS__, 'include_template']);

        // Schedule event action
        add_action('knife_schedule_cents', [__CLASS__, 'sync_database']);

        // Update entry-content with cents posts
        add_filter('the_content', [__CLASS__, 'show_cents'], 5);

        // Define cents settings if still not
        if(!defined('KNIFE_CENTS')) {
            define('KNIFE_CENTS', []);
        }
    }


    /**
     * Schedule cents sync event
     */
    public static function schedule_sync() {
        if(!wp_next_scheduled('knife_schedule_cents')) {
            wp_schedule_event(time(), 'minutely', 'knife_schedule_cents');
        }
    }


    /**
     * Add cron interval for every minute execution
     */
    public static function add_cron_interval($schedules) {
        if(empty($schedules['minutely'])) {
            $schedules['minutely'] = [
                'interval' => 60,
                'display' => __('Каждую минуту', 'knife-theme')
            ];
        }

        return $schedules;
    }


    /**
     * Sync database on cron event
     */
    public static function sync_database() {
        if(empty(KNIFE_CENTS['page'])) {
            return false;
        }

        // Get page object by slug
        $page = get_page_by_path(KNIFE_CENTS['page']);

        if(empty($page->ID)) {
            return false;
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

        if(!empty($db->error)) {
            return false;
        }

        // Get cards from remote database
        $query = "SELECT title, content, source, link FROM messages ORDER BY created DESC";
        $cents = $db->get_results($query, ARRAY_A);

        if(!empty($cents)) {
            update_post_meta($page->ID, self::$meta_cents, $cents);
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
     * Update entry-content with cents posts
     */
    public static function show_cents($content) {
        if(empty(KNIFE_CENTS['page']) || !is_page(KNIFE_CENTS['page'])) {
            return $content;
        }

        $page_id = get_the_ID();

        // Get post cents from post meta
        $cents = get_post_meta($page_id, self::$meta_cents, true);

        if(empty($cents)) {
            return $content;
        }

        // Create new content from cards
        $content = self::create_cards($cents, '</div><div class="entry-content">');

        return $content;
    }


    /**
     * Create cards using post cents data
     */
    private static function create_cards($cents, $separator) {
        $cards = [];

        foreach($cents as $i => $cent) {
            $title = sprintf(
                '<h3 id="%1$d" data-before="#%1$d"><em>%2$s</em></h3>',
                count($cents) - $i, esc_html($cent['title'])
            );

            // Append title to content
            $card = $title . wpautop(esc_html($cent['content']));

            // Add source button if exists
            if(!empty($cent['link']) && !empty($cent['source'])) {
                $card = $card . self::create_button($cent['link'], $cent['source']);
            }

            $cards[] = $card;
        }

        return implode($separator, $cards);
    }


    /**
     * Append button to cards if need
     */
    private static function create_button($link, $source) {
        $button = sprintf(
            '<a class="button" href="%2$s" target="_blank" data-before="%3$s">%1$s</a>',
            esc_html($source), esc_url($link),
            __('Источник ', 'knife-theme')
        );

        $figure = sprintf(
            '<figure class="figure figure--source">%s</figure>',
            wp_targeted_link_rel($button)
        );

        return $figure;
    }
}


/**
 * Load current module environment
 */
Knife_Cents_Page::load_module();
