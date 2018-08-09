<?php
/**
* TinyMCE Buttons
*
* Add custom TinyMCE buttons
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_MCE_Plugins {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function init() {
        // Button tag
        add_action('admin_init', [__CLASS__, 'init_mce']);

        // Configure global tinymce
        add_filter('tiny_mce_before_init', [__CLASS__, 'configure_tinymce']);
    }


    /**
     * Init tinymce plugins
     */
    public static function init_mce() {
        if(get_user_option('rich_editing') !== 'true') {
            return;
        }

        add_filter('mce_buttons', [__CLASS__, 'register_buttons']);
        add_filter('mce_external_plugins', [__CLASS__, 'add_plugins']);

        add_action('admin_enqueue_scripts', [__CLASS__, 'add_scripts']);
    }


    /**
     * Add tinymce configure scripts
     */
    public static function add_scripts($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        wp_enqueue_script('knife-mce-reinit', $include . '/scripts/mce-reinit.js', [], $version);
    }


    /**
     * Add js plugins file
     */
    public static function add_plugins($plugins) {
        $include = get_template_directory_uri() . '/core/include';

        $plugins['pushbutton'] = $include . '/scripts/mce-pushbutton.js';

        return $plugins;
    }


    /**
     * Register buttons in editor panel
     */
    public static function register_buttons($buttons) {
        array_push($buttons, 'pushbutton');

        return $buttons;
    }


    /**
     * Remove annoying span tags after google docs pasting
     */
    public static function configure_tinymce($settings) {
        $settings['invalid_styles'] = 'color font-weight font-size';

        return $settings;
    }
}


Knife_MCE_Plugins::init();
