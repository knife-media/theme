<?php
/**
* TinyMCE Buttons
*
* Add custom TinyMCE buttons
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_MCE_Buttons;

class Knife_MCE_Buttons {
    public function __construct() {
        // button tag
        add_action('admin_init', [$this, 'init_mce']);

        // configure global tinymce
        add_filter('tiny_mce_before_init', [$this, 'configure_tinymce']);
    }


    /**
     * Init tinymce plugins
     */
    public function init_mce() {
        if(!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        if(get_user_option('rich_editing') !== 'true')
            return;

        add_filter('mce_buttons', [$this, 'register_buttons']);
        add_filter('mce_external_plugins', [$this, 'add_plugins']);
    }


    /**
     * Add js plugins file
     */
    public function add_plugins($plugins) {
        $include = get_template_directory_uri() . '/core/include';

        $plugins['push'] = $include . '/scripts/mce-buttons-push.js';

        return $plugins;
    }


    /**
     * Register buttons in editor panel
     */
    public function register_buttons($buttons) {
        array_push($buttons, 'push');

        return $buttons;
    }


    /**
     * Remove annoying span tags after google docs pasting
     */
    public function configure_tinymce($settings) {
        $settings['invalid_styles'] = 'color font-weight font-size';

        return $settings;
    }
}
