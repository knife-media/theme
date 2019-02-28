<?php
/**
 * Add custom author fields
 *
 * @package knife-theme
 * @since 1.7
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Author_Meta {
    /**
     * Unique meta to store user photo
     *
     * @access  private
     * @var     string
     */
    private static $user_photo = '_knife-user-photo';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Update authors contact info
        add_filter('user_contactmethods', [__CLASS__, 'add_user_contacts']);

        // Add user photo options
        add_action('admin_init', [__CLASS__, 'add_user_options']);
    }


    /**
     * User custom options
     */
    public static function add_user_options() {
        if(current_user_can('edit_users')) {
            // Print user options row
            add_action('show_user_profile', [__CLASS__, 'print_user_options']);
            add_action('edit_user_profile', [__CLASS__, 'print_user_options']);
            add_action('user_new_form', [__CLASS__, 'print_user_options']);

            // Save user option
            add_action('profile_update', [__CLASS__, 'save_user_meta']);
            add_action('user_register', [__CLASS__, 'save_user_meta']);

            // Enqueue post metabox scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_user_assets']);
        }
    }


    /**
     * Update authors contact info
     */
    public static function add_user_contacts($contact) {
        $contact['vkontakte'] = __('Ссылка на ВКонтакте', 'knife-theme');
        $contact['facebook'] = __('Ссылка на Facebook', 'knife-theme');
        $contact['telegram'] = __('Профиль Telegram', 'knife-theme');
        $contact['instagram'] = __('Профиль Instagram', 'knife-theme');
        $contact['twitter'] = __('Профиль Twitter', 'knife-theme');

        return $contact;
    }


    /**
     * Add user photo field with button
     */
    public static function print_user_options($user) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/author-options.php');
    }


    /**
     * Save user meta
     */
    public static function save_user_meta($user_id) {
        if(isset($_REQUEST[self::$user_photo])) {
            $photo = sanitize_text_field($_REQUEST[self::$user_photo]);

            update_user_meta($user_id, self::$user_photo, $photo);
        }
    }


    /**
     * Enqueue user options assets
     */
    public static function enqueue_user_assets($hook) {
        if(!in_array($hook, ['user-edit.php', 'user-new.php', 'profile.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin scripts
        wp_enqueue_script('knife-author-options', $include . '/scripts/author-options.js', ['jquery'], $version);

        $options = [
            'choose' => __('Выберите фото пользователя', 'knife-theme')
        ];

        wp_localize_script('knife-author-options', 'knife_author_options', $options);
    }
}


/**
 * Load current module environment
 */
Knife_Author_Meta::load_module();
