<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.16
 */


/**
 * Set public post preview plugin link ttl
 *
 * @link https://wordpress.org/plugins/public-post-preview
 * @since 1.13
 */
add_filter('ppp_nonce_life', function() {
    return 60 * 60 * 24 * 180; // 180 days
});


/**
 * Hide settings for Social Planner plugin
 *
 * @since 1.14
 */
add_filter('social_planner_hide_settings', '__return_true');


/**
 * Send Social Planner errors to telegram bot
 */
add_action('social_planner_task_sent', function($results, $key, $post_id) {
    $secret = empty(KNIFE_REQUESTS['secret']) ? '' : KNIFE_REQUESTS['secret'];

    if(empty($results[$key]['errors'])) {
        return;
    }

    $errors = [];

    foreach ($results[$key]['errors'] as $key => $message) {
        $title = $key;

        if(class_exists('Social_Planner\Settings')) {
            $providers = Social_Planner\Settings::get_providers();

            $class = Social_Planner\Core::get_network_class($key);
            $label = Social_Planner\Core::get_network_label($class);

            if(!empty($providers[$key]['title'])) {
                $label = $label . '. ' . $providers[$key]['title'];
            }
        }

        $errors[] = "{$label}: {$message}";
    }

    $timestamp = time();

    $data = [
        'nonce' => substr(sha1($secret . $timestamp), -12, 10),
        'time' => $timestamp,
        'errors' => json_encode($errors),
        'link' => get_permalink($post_id)
    ];

    wp_remote_post('https://knife.media/requests/planner', [
        'body' => http_build_query($data),
        'blocking' => false,
    ]);
}, 10, 3);


/**
 * Send notification if the post is not planned to Social Planner
 */
add_action('transition_post_status', function($new_status, $old_status, $post) {
    if($new_status === $old_status) {
        return;
    }

    if($new_status !== 'publish') {
        return;
    }

    if(!has_category('news', $post->ID)) {
        return;
    }

    wp_schedule_single_event(time() + 30, 'knife_schedule_requests_news', [$post->ID]);
}, 20, 3);


/**
 * Hide Social Planner metabox from pages.
 *
 * @since 1.14
 */
add_filter('social_planner_post_types', function($post_types) {
    foreach($post_types as $i => $name) {
        if('page' === $name) {
            unset($post_types[$i]);
        }
    }

    return $post_types;
});


/**
 * Add promo hashtag to Social Planner message
 *
 * @since 1.14
 */
add_filter('social_planner_prepare_excerpt', function($excerpt, $message) {
    $post_id = $message['post_id'];

    if(!class_exists('Knife_Promo_Manager')) {
        return $excerpt;
    }

    $meta_promo = Knife_Promo_Manager::$meta_promo;

    if(get_post_meta($post_id, $meta_promo, true)) {
        $hashtag =  __('#партнерскийматериал', 'knife-theme');

        if (!empty($excerpt)) {
            $excerpt = $excerpt . "\n\n" . $hashtag;
        }
    }

    return $excerpt;
}, 10, 2);


/**
 * Social Planner forgotten news scheduler event
 *
 * @since 1.15
 */
add_action('knife_schedule_requests_news', function($post_id) {
    $secret = empty(KNIFE_REQUESTS['secret']) ? '' : KNIFE_REQUESTS['secret'];

    if(!class_exists('Social_Planner\Metabox')) {
        return;
    }

    $tasks = Social_Planner\Metabox::get_tasks($post_id);

    if(!empty($tasks)) {
        return;
    }

    $timestamp = time();

    $data = [
        'nonce' => substr(sha1($secret . $timestamp), -12, 10),
        'time' => $timestamp,
        'link' => get_permalink($post_id)
    ];

    wp_remote_post('https://knife.media/requests/news', [
        'body' => http_build_query($data),
        'blocking' => false,
    ]);
});


/**
 * Replace post link to shorten version for Telegram
 *
 * @since 1.16
 */
add_filter('social_planner_prepare_message', function($message, $target) {
    list($network, $name) = explode('-', $target);

    if ($network === 'telegram') {
        $message['link'] = 'https://knf.md/tg' . wp_make_link_relative($message['link']);
    }

    return $message;
}, 10, 2);


/**
 * Add channel link to Telegram main messages
 *
 * @since 1.16
 */
add_filter('social_planner_prepare_message', function($message, $target) {
    if ($target === 'telegram-main' && !empty($message['excerpt'])) {
        $message['excerpt'] = $message['excerpt'] . "\n\n" . '🔪 @knifemedia';
    }

    return $message;
}, 10, 2);
