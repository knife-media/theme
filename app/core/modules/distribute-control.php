<?php
/**
 * Distribute control
 *
 * Schedule posts publishing to social networks
 *
 * @package knife-theme
 * @since 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Distribute_Control {
    /**
     * Default post type with social distribute metabox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-distribute-nonce';


   /**
    * Cancel scheduled event ajax action
    *
    * @access  private
    * @var     string
    */
    private static $ajax_action = 'knife-distribute-cancel';


    /**
     * Unique meta to store distribute items
     *
     * @access  private
     * @var     string
     */
    private static $meta_items = '_knife-distribute-items';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom distribute metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox'], 10, 2);

        // Cancel scheduled event
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'cancel_scheduled']);


        // Schedule event action
        add_action('knife_schedule_distribution', [__CLASS__, 'publish_task'], 10, 2);

        // Define distribute settings if still not
        if(!defined('KNIFE_DISTRIBUTE')) {
            define('KNIFE_DISTRIBUTE', []);
        }
    }


    /**
     * Add custom distribute metabox for editors and admins
     */
    public static function add_metabox() {
        if(current_user_can('publish_pages')) {
            add_meta_box('knife-distribute-metabox', __('Настройки кросспостинга'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');

            // Enqueue post metabox scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        }
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        // Current screen object
        $screen = get_current_screen();

        if(!in_array($screen->post_type, self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-distribute-metabox', $include . '/styles/distribute-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-distribute-metabox', $include . '/scripts/distribute-metabox.js', ['jquery'], $version);

        $options = [
            'post_id' => absint($post_id),
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$metabox_nonce),
            'meta_items' => esc_attr(self::$meta_items),

            'choose' => __('Выберите изображение', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-distribute-metabox', 'knife_distribute_metabox', $options);
    }


    /**
     * Display distribute metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/distribute-metabox.php');
    }


    /**
     * Create result posters using ajax options
     */
    public static function cancel_scheduled() {
        check_admin_referer(self::$metabox_nonce, 'nonce');

        foreach(['uniqid', 'post_id', 'timestamp'] as $required) {
            if(empty($_POST[$required])) {
                wp_send_json_error(__('Отсутствуют необходимые параметры запроса', 'knife-theme'));
            }
        }

        $timestamp = absint($_POST['timestamp']);

        // Unschedule event
        wp_unschedule_event($timestamp, 'knife_schedule_distribution', [
            sanitize_text_field($_POST['uniqid']), absint($_POST['post_id'])
        ]);

        wp_send_json_success();
    }


    /**
     * Publish scheduled task
     */
    public static function publish_task($post_id, $uniqid) {
        print_r('test');
        file_put_contents(ABSPATH . "log.txt", 'test', FILE_APPEND);
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id, $post) {
        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('publish_pages', $post_id)) {
            return;
        }

        // Sanitize items request
        $items = self::sanitize_items(self::$meta_items);

        if(count($items) > 0) {
            // Delete distribute post meta to create it again below
            delete_post_meta($post_id, self::$meta_items);

            foreach($items as $item) {
                // Schedule task if need
                $item = self::schedule_task($item, $post_id, $post);

                // Add post meta
                add_post_meta($post_id, self::$meta_items, $item);
            }
        }
    }


    /**
     * Update distribute items from post-metabox
     */
    private static function sanitize_items($query, $items = []) {
        if(empty($_REQUEST[$query])) {
            return $items;
        }

        foreach($_REQUEST[$query] as $meta) {
            $item = [];

            // Generate new item uniqid if empty
            if(empty($meta['uniqid'])) {
                $meta['uniqid'] = uniqid();
            }

            if(isset($meta['network'])) {
                foreach((array) $meta['network'] as $network) {
                    if(array_key_exists($network, KNIFE_DISTRIBUTE)) {
                        $item['network'][] = $network;
                    }
                }
            }

            if(isset($meta['excerpt'])) {
                $item['excerpt'] = sanitize_textarea_field($meta['excerpt']);
            }

            if(isset($meta['attachment'])) {
                $item['attachment'] = absint($meta['attachment']);
            }

            if(isset($meta['delay'])) {
                $item['delay'] = absint($meta['delay']);
            }

            // Add non-empty post meta
            if(array_filter($item)) {
                $items[] = array_merge($item, [
                    'uniqid' => $meta['uniqid']
                ]);
            }
        }

        return $items;
    }


    /**
     * Schedule task by post meta data
     */
    private static function schedule_task($item, $post_id, $post) {
        $args = [
            $item['uniqid'], $post_id
        ];

        $scheduled = wp_next_scheduled('knife_schedule_distribution', $args);

        // Remove event if already scheduled
        if($scheduled !== false) {
            wp_unschedule_event($scheduled, 'knife_schedule_distribution', $args);
        }

        // Skip not delayed posts or empty networks
        if(empty($item['delay']) || empty($item['network'])) {
            return $item;
        }

        $status = $post->post_status;

        // Skip drafts and private posts here
        if(in_array($status, ['future', 'publish'])) {
            $timestamp = time();

            if($status === 'future') {
                $timestamp = strtotime($post->post_date_gmt);
            }

            // Get timestamp
            $timestamp = $timestamp + $item['delay'] * 60;

            // Schedule event
            wp_schedule_single_event($timestamp, 'knife_schedule_distribution', $args);
        }

        return $item;
    }
}


/**
 * Load module
 */
Knife_Distribute_Control::load_module();
