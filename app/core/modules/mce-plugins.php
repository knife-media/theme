<?php
/**
 * TinyMCE Buttons
 *
 * Add custom TinyMCE buttons
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.11
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
        'push-button', 'mark-button', 'quote-button', 'card-separator', 'similar-block', 'reference-block', 'script-embed'
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

            add_action('admin_enqueue_scripts', [__CLASS__, 'add_scripts']);
        }
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

        // Add custom plugins
        $custom = self::$helpers + self::$buttons;

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

        return $settings;
    }
}


Knife_MCE_Plugins::init();
