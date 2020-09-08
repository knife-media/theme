<?php
/**
 * TinyMCE Buttons
 *
 * Add custom TinyMCE buttons
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.14
 */


if (!defined('WPINC')) {
    die;
}


class Knife_MCE_Plugins {
    /**
     * Custom buttons plugins
     *
     * @since 1.10
     */
    private static $helpers = ['figure-helper'];


    /**
     * Custom helpers plugins
     *
     * @since 1.10
     */
    private static $buttons = [
        'push-button',
        'mark-button',
        'quote-button',
        'card-separator',
        'remark-block',
        'similar-block',
        'reference-block',
        'script-embed'
    ];


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
        if(get_user_option('rich_editing') === 'true') {
            add_filter('mce_buttons', [__CLASS__, 'register_buttons']);
            add_filter('mce_external_plugins', [__CLASS__, 'add_plugins']);
        }
    }


    /**
     * Add js plugins file
     */
    public static function add_plugins($plugins) {
        $include = get_template_directory_uri() . '/core/include';

        // Add custom plugins
        $custom = array_merge(self::$helpers, self::$buttons);

        foreach($custom as $name) {
            $plugins[$name] = $include . "/tinymce/{$name}.js";
        }

        return $plugins;
    }


    /**
     * Register buttons in editor panel
     */
    public static function register_buttons($buttons) {
        $buttons = array_merge($buttons, self::$buttons);

        // Remove blockquote button
        if(($key = array_search('blockquote', $buttons)) !== false) {
            unset($buttons[$key]);
        }

        // Remove more button
        if(($key = array_search('wp_more', $buttons)) !== false) {
            unset($buttons[$key]);
        }

        return $buttons;
    }


    /**
     * Remove annoying span tags after google docs pasting
     */
    public static function configure_tinymce($settings) {
        $settings['invalid_styles'] = 'color font-weight font-size';
        $settings['valid_children'] = '-aside[aside]';

        $include = get_template_directory() . '/core/include';

        // Add paste preprocess script if exists
        if(file_exists($include . '/tinymce/paste-preprocess.js')) {
            $settings['paste_preprocess'] = file_get_contents($include . '/tinymce/paste-preprocess.js');
        }

        return $settings;
    }
}


Knife_MCE_Plugins::init();
