<?php
/**
 * Social delivery
 *
 * Send notifications to social networks channels
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.8
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Social_Delivery {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Define social delivery settings if still not
        if(!defined('KNIFE_DELIVERY')) {
            define('KNIFE_DELIVERY', []);
        }

        // Die if curl not installed
        if(!function_exists('curl_version')) {
            wp_die(__('Для нормальной работы темы необходимо установить модуль php-curl', 'knife-theme'));
        }
    }


    /**
     * Send message to facebook.com
     */
    public static function send_facebook($group, $message, $poster = false, $delivery = 'facebook') {
        $api = 'https://graph.facebook.com/v3.2/' . $group;

        // Get delivery settings
        $conf = KNIFE_DELIVERY[$delivery] ?? [];

        // Check token format
        if(empty($conf['token']) || !preg_match('~^[0-9a-z]+$~i', $conf['token'])) {
            return new WP_Error('token', __('Неверный формат токена', 'knife-theme'));
        }

        // Append token to post fields
        $message['access_token'] = $conf['token'];

        // Setup default endpoint
        $endpoint = '/feed';

        if($poster) {
            $message['source'] = new CURLFile($poster);

            // Update endpoint
            $endpoint = '/photos';
        }

        $response = self::make_request($api . $endpoint, $message);

        // Try to parse response
        $response = @json_decode($response);

        if(!empty($response->id)) {
            return $response->id;
        }

        if(!empty($response->error->message)) {
            return new WP_Error('request', $response->error->message);
        }

        return new WP_Error('request', __('Неизвестная ошибка запроса', 'knife-theme'));
    }


    /**
     * Send message to Telegram
     */
    public static function send_telegram($group, $message, $poster = false, $delivery = 'telegram') {
        // Get delivery settings
        $conf = KNIFE_DELIVERY[$delivery] ?? [];

        // Check token format
        if(empty($conf['token']) || !preg_match('~^[0-9]{1,}:[\w\-]+$~', $conf['token'])) {
            return new WP_Error('token', __('Неверный формат токена', 'knife-theme'));
        }

        $api = 'https://api.telegram.org/bot' . $conf['token'];

        // Append chat_id to message
        $message['chat_id'] = $group;

        // Setup default endpoint
        $endpoint = '/sendMessage';

        if($poster) {
            $message['photo'] = new CURLFile($poster);

            // Update endpoint
            $endpoint = '/sendPhoto';
        }

        $response = self::make_request($api . $endpoint, $message);

        // Try to parse response
        $response = @json_decode($response);

        if(!empty($response->result->message_id)) {
            return $response->result->message_id;
        }

        if(!empty($response->description)) {
            return new WP_Error('request', $response->description);
        }

        return new WP_Error('request', __('Неизвестная ошибка запроса', 'knife-theme'));
    }


    /**
     * Post to vk.com wall
     */
    public static function send_vkontakte($group, $message, $poster = false, $delivery = 'vkontakte') {
        $api = 'https://api.vk.com/method';

        // Get delivery settings
        $conf = KNIFE_DELIVERY[$delivery] ?? [];

        // Check token format
        if(empty($conf['token']) || !preg_match('~^[0-9a-z]+$~', $conf['token'])) {
            return new WP_Error('token', __('Неверный формат токена', 'knife-theme'));
        }

        // Define required api settings
        $settings = [
            'access_token' => $conf['token'],
            'group_id' => $group,
            'v' => '5.95'
        ];

        if($poster) {
            // Get wall upload server
            // https://vk.com/dev/photos.getWallUploadServer
            $response = self::make_request($api . '/photos.getWallUploadServer', $settings);

            // Try to parse response
            $response = @json_decode($response);

            if(empty($response->response->upload_url)) {
                return new WP_Error('request', __('Не удалось получить сервер для загрузки', 'knife-theme'));
            }

            $options = ['photo' => new CURLFile($poster)];

            // Try to upload file
            $response = self::make_request($response->response->upload_url, $options);

            // Check if curl is not ok
            if($response === false) {
                return new WP_Error('request', __('Не удалось загрузить изображение', 'knife-theme'));
            }

            $options = (array) json_decode($response);

            // Save wall photo
            // https://vk.com/dev/photos.saveWallPhoto
            $response = self::make_request($api . '/photos.saveWallPhoto', $settings + $options);

            // Parse response
            $response = @json_decode($response);

            if(empty($response->response[0])) {
                return new WP_Error('request', __('Не удалось сохранить изображение', 'knife-theme'));
            }

            $options = $response->response[0];

            if(empty($options->owner_id) || empty($options->id)) {
                return new WP_Error('request', __('Не удалось сохранить изображение', 'knife-theme'));
            }

            $attachment = 'photo' . $options->owner_id . '_' . $options->id;

            if(empty($message['attachments'])) {
                $message['attachments'] = $attachment;
            }

            $message['attachments'] = $message['attachments'] . ',' . $attachment;
        }

        $response = self::make_request($api . '/wall.post', $settings + $message);

        // Try to parse response
        $response = @json_decode($response);

        if(!empty($response->response->post_id)) {
            return $response->response->post_id;
        }

        if(!empty($response->error->error_msg)) {
            return new WP_Error('request', $response->error->error_msg);
        }

        return new WP_Error('request', __('Неизвестная ошибка запроса', 'knife-theme'));
    }


    /**
     * Send curl request
     */
    private static function make_request($url, $postfields = null) {
        $version = wp_get_theme()->get('Version');

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_USERAGENT => 'knife-theme/' . $version . get_bloginfo('url')
        ];

        if(is_array($postfields)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }

        $handler = curl_init();
        curl_setopt_array($handler, $options);

        return curl_exec($handler);
    }
}


/**
 * Load module
 */
Knife_Social_Delivery::load_module();
