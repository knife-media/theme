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
        add_action('knife_schedule_distribution', [__CLASS__, 'launch_task'], 10, 2);

        // Define distribute settings if still not
        if(!defined('KNIFE_DISTRIBUTE')) {
            define('KNIFE_DISTRIBUTE', []);
        }

       self::launch_task('5ccaf04bc179e', 68530); exit;
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

        foreach(['uniqid', 'post_id'] as $required) {
            if(empty($_POST[$required])) {
                wp_send_json_error(__('Отсутствуют необходимые параметры запроса', 'knife-theme'));
            }
        }

        $settings = [
            sanitize_title($_POST['uniqid']), absint($_POST['post_id'])
        ];

        // Find scheduled timestamp
        $scheduled = wp_next_scheduled('knife_schedule_distribution', $settings);

        // Unschedule event
        wp_unschedule_event($scheduled, 'knife_schedule_distribution', $settings);

        wp_send_json_success();
    }


    /**
     * Publish scheduled task
     */
    public static function launch_task($uniqid, $post_id) {
        // Get distribute items
        $items = (array) get_post_meta($post_id, self::$meta_items, true);

        // Skip empty and already sent tasks
        if(isset($items[$uniqid]) && empty($items[$uniqid]['sent'])) {
            $item = wp_parse_args((array) $items[$uniqid], [
                'networks' => [],
                'excerpt' => '',
                'attachment' => 0
            ]);

            $results = [];

            // Loop through networks and send tasks
            foreach($item['networks'] as $network) {
                // Try to send message
                $results[$network] = self::send_task($item, $network, $post_id);
            }

            $items[$uniqid] = array_merge($items[$uniqid], [
                'results' => $results,
                'sent' => time()
            ]);

//            update_post_meta($post_id, self::$meta_items, $items);
        }

        print_r($items);
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

        if(empty($_REQUEST[self::$meta_items])) {
            return;
        }

        // Get existing items
        $items = (array) get_post_meta($post_id, self::$meta_items, true);

        // Sanitize items request
        $items = self::sanitize_items($items, self::$meta_items);

        // Schedule tasks if need
        $items = self::schedule_tasks($items, $post, $post_id);

        // Update post meta
        update_post_meta($post_id, self::$meta_items, $items);
    }


    /**
     * Update distribute items from post-metabox
     */
    private static function sanitize_items($items, $query) {
        $requests = [];

        // Normalize requests array
        foreach((array) $_REQUEST[$query] as $request) {
            $item = [];

            // Generate new item uniqid if empty
            if(empty($request['uniqid'])) {
                $request['uniqid'] = uniqid();
            }

            $uniqid = $request['uniqid'];

            if(isset($request['networks'])) {
                foreach((array) $request['networks'] as $network) {
                    if(array_key_exists($network, KNIFE_DISTRIBUTE)) {
                        $item['networks'][] = $network;
                    }
                }
            }

            if(isset($request['excerpt'])) {
                $item['excerpt'] = sanitize_textarea_field($request['excerpt']);
            }

            if(isset($request['attachment'])) {
                $item['attachment'] = absint($request['attachment']);
            }

            if(isset($request['delay'])) {
                $item['delay'] = absint($request['delay']);
            }

            $requests[$uniqid] = $item;
        }

        // Save only the necessary items
        foreach($requests as $uniqid => &$request) {
            if(isset($items[$uniqid]['sent'])) {
                $request = $items[$uniqid];
            }

            if(!array_filter($request)) {
                unset($requests[$uniqid]);
            }
        }

        return $requests;
    }


    /**
     * Schedule task by post meta data
     */
    private static function schedule_tasks($items, $post, $post_id) {
        foreach($items as $uniqid => $item) {
            // Skip if alread scheduled
            $scheduled = wp_next_scheduled('knife_schedule_distribution', [$uniqid, $post_id]);

            if($scheduled !== false) {
                continue;
            }

            // Skip not delayed posts or empty networks
            if(empty($item['delay']) || empty($item['networks'])) {
                continue;
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
                wp_schedule_single_event($timestamp, 'knife_schedule_distribution', [$uniqid, $post_id]);
            }
        }

        return $items;
    }


    /**
     * Send scheduled task
     */
    private static function send_task($task, $network, $post_id) {
/*        if(empty(KNIFE_DISTRIBUTE[$network]) {
            continue;
        }

        $options = KNIFE_DISTRIBUTE[$network];
 */

        $permalink = get_permalink($post_id);
        $text = $task['excerpt'] . "\n\n" . $permalink;


            $chat_id = KNIFE_DISTRIBUTE[$network]['group'];

        $url        = $bot_url . "sendPhoto?chat_id=" . $chat_id;

        $post_fields = array('chat_id'   => $chat_id,
            'photo'     => new CURLFile('/tmp/ph.jpg'),
            'caption' => $text
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        print_r($output);

/*
        if(method_exists('Knife_Notifier_Robot', 'send_telegram')) {

            $message = [
                'chat_id' => $chat_id,
                'text' => $text
            ];

//            Knife_Notifier_Robot::send_telegram($message);
        }
*/


        print_r($task);
        exit;

        return $permalink;
    }
}


/**
 * Load module
 */
Knife_Distribute_Control::load_module();
