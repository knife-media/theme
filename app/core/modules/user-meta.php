<?php
/**
 * Add custom user fields
 *
 * @package knife-theme
 * @since 1.7
 */


if (!defined('WPINC')) {
    die;
}


class Knife_User_Meta {
    /**
     * Unique meta to store user photo
     *
     * @access  private
     * @var     string
     */
    private static $user_photo = '_knife-user-photo';


   /**
     * Ajax action
     *
     * @access  private
     * @var     string
     */
    private static $ajax_action = 'knife-user-photo';


    /**
     * Unique nonce string using for ajax referer check
     *
     * @access  private
     * @var     string
     */
    private static $ajax_nonce = 'knife-user-options-nonce';


    /**
     * Directory to save user photo avatars
     *
     * @access  private
     * @var     string
     */
    private static $upload_folder = '/avatars/';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Update users contact info
        add_filter('user_contactmethods', [__CLASS__, 'add_user_contacts']);

        // Add user photo options
        add_action('admin_init', [__CLASS__, 'add_user_options']);

        // Generate user photo
        add_action('wp_ajax_' . self::$ajax_action, [__CLASS__, 'generate_user_photo']);

        // Update author description links with target attribute
        add_filter('get_the_author_description', [__CLASS__, 'update_user_description']);
    }


    /**
     * User custom options
     */
    public static function add_user_options() {
        if(current_user_can('edit_users')) {
            // Print user options row
            add_action('show_user_profile', [__CLASS__, 'print_user_options']);
            add_action('edit_user_profile', [__CLASS__, 'print_user_options']);

            // Save user option
            add_action('profile_update', [__CLASS__, 'save_user_meta']);

            // Enqueue user options scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_user_assets']);
        }
    }


    /**
     * Update author description links with target attribute
     *
     * @link https://core.trac.wordpress.org/ticket/12056
     */
    public static function update_user_description($description) {
        $description = str_replace('<a', '<a target="_blank" rel="noopener"', $description);

        return $description;
    }


    /**
     * Update users contact info
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

        include_once($include . '/templates/user-options.php');
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
        if(!in_array($hook, ['user-edit.php', 'profile.php'])) {
            return;
        }

        global $user_id;

        if(absint($user_id) < 1) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin scripts
        wp_enqueue_script('knife-user-options', $include . '/scripts/user-options.js', ['jquery'], $version);

        $options = [
            'user_id' => $user_id,
            'action' => esc_attr(self::$ajax_action),
            'nonce' => wp_create_nonce(self::$ajax_nonce),
            'choose' => __('Выберите фото пользователя', 'knife-theme'),
            'error' => __('Непредвиденная ошибка сервера', 'knife-theme')
        ];

        wp_localize_script('knife-user-options', 'knife_user_options', $options);
    }


    /**
     * Get postid by url using admin side ajax
     */
    public static function generate_user_photo() {
        check_admin_referer(self::$ajax_nonce, 'nonce');

        if(!isset($_POST['poster'], $_POST['user_id'])) {
            wp_send_json_error();
        }

        $user_id = absint($_POST['user_id']);

        // Check upload folder
        $upload = wp_upload_dir();

        // Upload dir
        $create = [
            'basedir' => $upload['basedir'] . self::$upload_folder,
            'baseurl' => $upload['baseurl'] . self::$upload_folder
        ];

        if(!is_dir($create['basedir']) && !mkdir($create['basedir'])) {
            wp_send_json_error(__('Проверьте права на запись папки загрузок', 'knife-theme'));
        }

        $filename = $user_id . uniqid('-') . '.jpg';

        // Get image url by attachment id
        $poster = wp_get_image_editor(get_attached_file($_POST['poster']));

        if(is_wp_error($poster)) {
            wp_send_json_error($poster->get_error_message());
        }

        $poster->resize(150, 150, true);
        $poster->save($create['basedir'] . $filename, 'image/jpeg');

        if(is_wp_error($poster)) {
            wp_send_json_error($poster->get_error_message());
        }

        wp_send_json_success($create['baseurl'] . $filename);
    }
}


/**
 * Load current module environment
 */
Knife_User_Meta::load_module();
