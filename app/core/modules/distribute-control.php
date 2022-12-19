<?php
/**
 * Distribute control
 *
 * Schedule posts publishing to social networks
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.16
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Distribute_Control {
    /**
     * Default post type with social distribute metabox
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'club', 'quiz'];


    /**
     * Metabox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-distribute-nonce';


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
     * Store task delay while scheduling
     *
     * @access  private
     * @var     array
     */
    private static $task_delay = [];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Cancel scheduled event
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'cancel_scheduled']);

        // Schedule event action
        add_action('knife_schedule_distribution', [__CLASS__, 'start_task'], 10, 2);

        if(current_user_can('unfiltered_html')) {
            // Add custom distribute metabox
            add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

            // Save metabox
            add_action('save_post', [__CLASS__, 'save_meta'], 10, 2);

            // Add dashboard widget with scheduled tasks
            add_action('wp_dashboard_setup', [__CLASS__, 'add_dashboard_widget']);

            // Enqueue metabox assets
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        }

        // Die if php-mb not installed
        if(!function_exists('mb_strlen')) {
            wp_die(__('Для нормальной работы темы необходимо установить модуль php-mb', 'knife-theme'));
        }

        // Define distribute settings if still not
        if(!defined('KNIFE_DISTRIBUTE')) {
            define('KNIFE_DISTRIBUTE', []);
        }
    }


    /**
     * Add dashboard widget with scheduled tasks
     */
    public static function add_dashboard_widget() {
        wp_add_dashboard_widget('knife-distribute-widget', __('Кросспостинг', 'knife-theme'), [__CLASS__, 'display_widget']);
    }


    /**
     * Add custom distribute metabox for editors and admins
     */
    public static function add_metabox() {
        add_meta_box('knife-distribute-metabox', __('Настройки кросспостинга', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'advanced');
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Dashboard widget styles
        if($hook === 'index.php') {
            // Insert admin styles
            wp_enqueue_style('knife-distribute-widget', $include . '/styles/distribute-widget.css', [], $version);
        }

        global $post;

        // Metabox styles
        if(in_array($hook, ['post.php', 'post-new.php'])) {
            $screen = get_current_screen();

            if(!in_array($screen->post_type, self::$post_type)) {
                return;
            }

            // Insert wp media scripts
            wp_enqueue_media();

            // Insert admin styles
            wp_enqueue_style('knife-distribute-metabox', $include . '/styles/distribute-metabox.css', [], $version);

            // Insert admin scripts
            wp_enqueue_script('knife-distribute-metabox', $include . '/scripts/distribute-metabox.js', ['jquery'], $version);

            $options = [
                'post_id' => absint($post->ID),
                'action' => esc_attr(self::$ajax_action),
                'nonce' => wp_create_nonce(self::$ajax_nonce),
                'meta_items' => esc_attr(self::$meta_items),

                'choose' => __('Выберите изображение', 'knife-theme'),
                'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
            ];

            wp_localize_script('knife-distribute-metabox', 'knife_distribute_metabox', $options);
        }
    }


    /**
     * Display dashboard widget
     */
    public static function display_widget() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/distribute-widget.php');
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
        check_admin_referer(self::$ajax_nonce, 'nonce');

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
     * Start scheduled task
     */
    public static function start_task($uniqid, $post_id) {
        // Get post items
        $items = get_post_meta($post_id, self::$meta_items, true);

        // Check if item task exists
        if(empty($items[$uniqid])) {
            return;
        }

        $item = $items[$uniqid];

        // Skip empty and already sent tasks
        if(isset($item['sent']) || empty($item['targets'])) {
            return;
        }

        $items[$uniqid]['sent'] = -1;

        // Should flag sent as soon as possible
        update_post_meta($post_id, self::$meta_items, $items);

        $results = [];

        // Loop through targets and send tasks
        foreach($item['targets'] as $target) {
            $sent = self::launch_task($item, $target, $post_id);

            if(is_wp_error($sent)) {
                $results['errors'][$target] = $sent->get_error_message();

                continue;
            }

            $results['complete'][$target] = $sent;
        }

        $items[$uniqid]['sent'] = time();

        // Upgrade item with results array
        $items[$uniqid] = $items[$uniqid] + $results;

        update_post_meta($post_id, self::$meta_items, $items);
    }


    /**
     * Save post options
     */
    public static function save_meta($post_id, $post) {
        if(wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(empty($_REQUEST[self::$meta_items])) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Sanitize items request
        $items = self::sanitize_items($_REQUEST[self::$meta_items]);

        // Schedule tasks if need
        $items = self::schedule_tasks($items, $post, $post_id);

        // Update post meta
        update_post_meta($post_id, self::$meta_items, $items);
    }


    /**
     * Update distribute items from post-metabox
     */
    private static function sanitize_items($items) {
        $tasks = [];

        // Normalize requests array
        foreach((array) $items as $item) {
            $task = [];

            // Generate new item uniqid if empty
            if(empty($item['uniqid'])) {
                $item['uniqid'] = uniqid();
            }

            $uniqid = $item['uniqid'];

            // Set task delay if need
            self::set_delay($uniqid, $item);

            if(isset($item['sent'])) {
                $task['sent'] = absint($item['sent']);
            }

            if(!empty($item['complete'])) {
                foreach((array) $item['complete'] as $network => $target) {
                    if(array_key_exists($network, KNIFE_DISTRIBUTE)) {
                        $task['complete'][$network] = sanitize_text_field($target);
                    }
                }
            }

            if(!empty($item['targets'])) {
                foreach((array) $item['targets'] as $network) {
                    if(array_key_exists($network, KNIFE_DISTRIBUTE)) {
                        $task['targets'][] = $network;
                    }
                }
            }

            if(!empty($item['excerpt'])) {
                $task['excerpt'] = sanitize_textarea_field($item['excerpt']);
            }

            if(!empty($item['attachment'])) {
                $task['attachment'] = absint($item['attachment']);
            }

            if(!empty($item['collapse'])) {
                $task['collapse'] = absint($item['collapse']);
            }

            $tasks[$uniqid] = $task;
        }

        // Save only the necessary items
        foreach($tasks as $uniqid => $task) {
            if(!array_filter($task)) {
                unset($tasks[$uniqid]);
            }
        }

        return $tasks;
    }


    /**
     * Schedule task by post meta data
     */
    private static function schedule_tasks($items, $post, $post_id) {
        foreach($items as $uniqid => $item) {
            $settings = [$uniqid, $post_id];

            // Skip already sent tasks
            if(isset($item['sent'])) {
                continue;
            }

            $scheduled = wp_next_scheduled('knife_schedule_distribution', $settings);

            // Skip tasks with empty targets
            if(empty($item['targets'])) {
                if($scheduled) {
                    wp_unschedule_event($scheduled, 'knife_schedule_distribution', $settings);
                }

                continue;
            }

            // Skip private and draft posts
            if(!in_array($post->post_status, ['future', 'publish'])) {
                if($scheduled) {
                    wp_unschedule_event($scheduled, 'knife_schedule_distribution', $settings);
                }

                continue;
            }

            $post_date = strtotime($post->post_date_gmt);

            // Reschedule time updated posts
            if($scheduled && $post_date > $scheduled) {
                wp_unschedule_event($scheduled, 'knife_schedule_distribution', $settings);

                $scheduled = strtotime('+ 15 minutes', $post_date);
                wp_schedule_single_event($scheduled, 'knife_schedule_distribution', $settings);
            }

            // Schedule new tasks
            if(!$scheduled && isset(self::$task_delay[$uniqid])) {
                $scheduled = max(time(), self::$task_delay[$uniqid]);

                if($post_date > $scheduled) {
                    $scheduled = strtotime('+ 15 minutes', $post_date);
                }

                wp_schedule_single_event($scheduled, 'knife_schedule_distribution', $settings);
            }
        }

        return $items;
    }


    /**
     * Set task delay while scheduling
     *
     * @version 1.9
     */
    private static function set_delay($uniqid, $request) {
        if(empty($request['date'])) {
            return;
        }

        if($request['date'] === 'now') {
            self::$task_delay[$uniqid] = current_time('U', true);
            return;
        }

        if(isset($request['hour'], $request['minute'])) {
            $format = sprintf('%s %s:%s:00',
                $request['date'], $request['hour'], $request['minute']
            );

            $delay = get_gmt_from_date($format, 'U');

            if($delay) {
                self::$task_delay[$uniqid] = $delay;
            }
        }
    }


    /**
     * Send scheduled task
     */
    private static function launch_task($item, $target, $post_id) {
        // Get distribute settings
        $conf = KNIFE_DISTRIBUTE[$target] ?? [];

        if(empty($conf['delivery'])) {
            return new WP_Error('config', __('Отсутствуют необходимые настройки дистрибуции', 'knife-theme'));
        }

        $prepare = 'prepare_' . $conf['delivery'];

        if(!method_exists(__CLASS__, $prepare)) {
            return new WP_Error('config', __('Не найден метод дистрибуции, указанный в настройках', 'knife-theme'));
        }

        return self::$prepare($conf, $item, $post_id);
    }


    /**
     * Prepare facebook message
     */
    private static function prepare_facebook($conf, $item, $post_id) {
        $link = get_permalink($post_id);

        // Check group id setting
        if(empty($conf['group'])) {
            return new WP_Error('config', __('Не найдены настройки группы', 'knife-theme'));
        }

        // Check if facebook social delivery method exists
        if(!method_exists('Knife_Social_Delivery', 'send_facebook')) {
            return new WP_Error('module', __('Не найден метод доставки', 'knife-theme'));
        }

        $message = [
            'message' => esc_url($link)
        ];

        if(empty($item['collapse'])) {
            $message['link'] = esc_url($link);

            // Remove default message link
            unset($message['message']);
        }

        if(!empty($item['excerpt'])) {
            $message['message'] = wp_specialchars_decode($item['excerpt']) . "\n\n" . esc_url($link);
        }

        // Add promo hashtag
        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if(get_post_meta($post_id, $meta_promo, true)) {
                $hashtag = __('#партнерскийматериал', 'knife-theme');

                // Append hashtag to message
                $message['message'] = $message['message'] . "\n\n" . $hashtag;
            }
        }

        $poster = false;

        if(!empty($item['attachment'])) {
            $poster = get_attached_file($item['attachment']);

            // Swap message to caption for message with poster
            if($poster) {
                $message['caption'] = $message['message'];

                // Unset useless message args
                unset($message['message'], $message['link']);
            }
        }


        $response = Knife_Social_Delivery::send_facebook($conf['group'], $message, $poster);

        // Try to sprintf response using config entry argument
        if(isset($conf['entry']) && !is_wp_error($response)) {
            $response = sprintf($conf['entry'], $response);
        }

        return $response;
    }


    /**
     * Prepare telegram message
     */
    private static function prepare_telegram($conf, $item, $post_id) {
        $link = get_permalink($post_id);

        // Check group id setting
        if(empty($conf['group'])) {
            return new WP_Error('config', __('Не найдены настройки группы', 'knife-theme'));
        }

        // Check if telegram social delivery method exists
        if(!method_exists('Knife_Social_Delivery', 'send_telegram')) {
            return new WP_Error('module', __('Не найден метод доставки', 'knife-theme'));
        }

        $message = [
            'parse_mode' => 'HTML',
            'text' => esc_url($link)
        ];

        if(!empty($item['collapse'])) {
            $message['disable_web_page_preview'] = true;
        }

        if(!empty($item['excerpt'])) {
            $message['text'] = wp_specialchars_decode($item['excerpt']);

            // Replace markdown entity with HTML tags
            $message['text'] = preg_replace('/\*(.+?)\*/s', '<b>$1</b>', $message['text']);
            $message['text'] = preg_replace('/_(.+?)_/s', '<i>$1</i>', $message['text']);

            $message['text'] = $message['text'] . "\n\n" . esc_url($link);
        }

        // Add promo hashtag
        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if(get_post_meta($post_id, $meta_promo, true)) {
                $hashtag = __('#партнерскийматериал', 'knife-theme');

                // Append hashtag to message
                $message['text'] = $message['text'] . "\n\n" . $hashtag;
            }
        }

        $poster = false;

        if(!empty($item['attachment']) && mb_strlen($message['text']) < 1024) {
            $poster = get_attached_file($item['attachment']);

            // Swap message to caption for message with poster
            if($poster) {
                $message['caption'] = $message['text'];

                // Unset useless message args
                unset($message['text']);
            }
        }


        $response = Knife_Social_Delivery::send_telegram($conf['group'], $message, $poster);

        // Try to sprintf response using config entry argument
        if(isset($conf['entry']) && !is_wp_error($response)) {
            $response = sprintf($conf['entry'], $response);
        }

        return $response;
    }


    /**
     * Prepare vkontakte message
     */
    private static function prepare_vkontakte($conf, $item, $post_id) {
        $link = get_permalink($post_id);

        // Check group id setting
        if(empty($conf['group'])) {
            return new WP_Error('config', __('Не найдены настройки группы', 'knife-theme'));
        }

        // Check if vkontakte social delivery method exists
        if(!method_exists('Knife_Social_Delivery', 'send_vkontakte')) {
            return new WP_Error('module', __('Не найден метод доставки', 'knife-theme'));
        }

        $message = [
            'owner_id' => '-' . $conf['group'],
            'from_group' => 1,
            'message' => esc_url($link)
        ];

        if(empty($item['collapse'])) {
            $message['attachments'] = esc_url($link);
        }

        if(!empty($item['excerpt'])) {
            $message['message'] = wp_specialchars_decode($item['excerpt']) . "\n\n" . esc_url($link);
        }

        // Add promo hashtag
        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if(get_post_meta($post_id, $meta_promo, true)) {
                $hashtag = __('#партнерскийматериал', 'knife-theme');

                // Append hashtag to message
                $message['message'] = $message['message'] . "\n\n" . $hashtag;
            }
        }

        if(!empty($item['attachment'])) {
            $poster = get_attached_file($item['attachment']);
        }

        $response = Knife_Social_Delivery::send_vkontakte($conf['group'], $message, $poster);

        // Try to sprintf response using config entry argument
        if(isset($conf['entry']) && !is_wp_error($response)) {
            $response = sprintf($conf['entry'], $response);
        }

        return $response;
    }


    /**
     * Prepare twitter message
     */
    private static function prepare_twitter($conf, $item, $post_id) {
        $link = get_permalink($post_id);

        // Check if twitter social delivery method exists
        if(!method_exists('Knife_Social_Delivery', 'send_twitter')) {
            return new WP_Error('module', __('Не найден метод доставки', 'knife-theme'));
        }

        $message = [];

        if(!empty($item['excerpt'])) {
            $message['status'] = wp_specialchars_decode($item['excerpt']) . "\n\n" . esc_url($link);
        }

        if(empty($message['status'])) {
            $message['status'] = esc_url($link);
        }

        // Add promo hashtag
        if(class_exists('Knife_Promo_Manager')) {
            $meta_promo = Knife_Promo_Manager::$meta_promo;

            if(get_post_meta($post_id, $meta_promo, true)) {
                $hashtag = __('#партнерскийматериал', 'knife-theme');

                // Append hashtag to message
                $message['status'] = $message['status'] . "\n\n" . $hashtag;
            }
        }

        $poster = false;

        if(!empty($item['attachment'])) {
            $poster = get_attached_file($item['attachment']);
        }


        $response = Knife_Social_Delivery::send_twitter($message, $poster);

        // Try to sprintf response using config entry argument
        if(isset($conf['entry']) && !is_wp_error($response)) {
            $response = sprintf($conf['entry'], $response);
        }

        return $response;
    }
}


/**
 * Load module
 */
Knife_Distribute_Control::load_module();
