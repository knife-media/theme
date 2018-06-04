<?php
/**
* User content
*
* Custom ugs club post type
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_User_Club;

class Knife_User_Club {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'club';


   /**
    * User form meta
    *
    * @since   1.3
    * @access  private
    * @var     string
    */
    private $meta = '_knife-user-form';


   /**
    * Ajax action
    *
    * @since   1.3
    * @access  private
    * @var     string
    */
    private $action = 'knife-user-form';


    /**
     * Unique option key to store user settings
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $option = 'knife-user-settings';


    /**
     * User form fields
     *
     * @since   1.3
     * @access  private
     * @var     array
     */
    private $fields = [];


    public function __construct() {

        // set form fields
                // print checkbox user form
        add_action('page_attributes_misc_attributes', [$this, 'print_checkbox']);

        // save user form post meta
        add_action('save_post', [$this, 'save_meta']);

        // append user form to content
        add_filter('wp_enqueue_scripts', [$this, 'inject_object'], 12);

        // receive user form with ajax
        add_action('wp_ajax_' . $this->action, [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_' . $this->action, [$this, 'submit_form']);

        // add settings page
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'add_menu']);

        // create new role once on switch theme
        add_action('after_setup_theme', [$this, 'create_role']);

        // register club post type
        add_action('init', [$this, 'register_club']);

        // add role caps
        add_action('admin_init', [$this, 'add_capabilities']);
    }


    /**
     * Create new user role
     */
    public function create_role() {
        add_role('club_user', __('Участник клуба', 'knife-theme'), [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => false,
        ]);
    }


    /**
     * Register story post type
     */
    public function register_club() {
        register_post_type($this->slug, [
            'labels'                => [
                'all_items'         => __('Все записи', 'knife-theme'),
            ],
            'label'                 => __('Клуб', 'knife-theme'),
            'description'           => __('Записи в клуб', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'editor', 'excerpt'],
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 10,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => ['club_item', 'club_items'],
            'map_meta_cap'          => true
        ]);
    }


    /**
     * Add role capabilities to user roles
     */
    public function add_capabilities() {
        $role = get_role('club_user');

        $role->add_cap('read_club_item');
        /*
        $role->add_cap('edit_club_item');

        $role->add_cap('edit_club_items');
        $role->add_cap('edit_other_club_items');
        $role->add_cap('edit_published_club_items');
        $role->add_cap('publish_club_items');
        $role->add_cap('read_private_club_items');
        $role->add_cap('delete_club_item');
        */
    }


    /**
     * Add push settings submenu to main options menu
     */
    public function add_menu() {
        add_submenu_page('options-general.php', __('Настройки клуба', 'knife-theme'), __('Настройки клуба', 'knife-theme'), 'manage_options', 'knife-club', [$this, 'settings_page']);
    }


    /**
     * Display push options page
     */
    public function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-user-settings');
        do_settings_sections('knife-user-settings');
        submit_button();

        echo '</form>';
    }


    /**
     * Register settings forms
     */
    public function settings_init() {
        register_setting('knife-user-settings', $this->option);

        add_settings_section(
            'knife-user-section',
            __('Настройки уведомлений', 'knife-theme'),
            [],
            'knife-user-settings'
        );

        add_settings_field(
            'telegram_token',
            __('Telegram Access Token', 'knife-theme'),
            [$this, 'setting_render_telegram_token'],
            'knife-user-settings',
            'knife-user-section'
        );

        add_settings_field(
            'telegram_chat',
            __('ID чата администраторов в Telegram', 'knife-theme'),
            [$this, 'setting_render_telegram_chat'],
            'knife-user-settings',
            'knife-user-section'
        );

        add_settings_field(
            'request_id',
            __('ID текущей заявки', 'knife-theme'),
            [$this, 'setting_render_request_id'],
            'knife-user-settings',
            'knife-user-section'
        );


    }

    public function setting_render_telegram_token() {
        $options = get_option($this->option);
        $default = isset($options['telegram_token']) ? $options['telegram_token'] : '';

        printf(
            '<input type="text" name="%1$s[telegram_token]" class="regular-text" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_telegram_chat() {
        $options = get_option($this->option);
        $default = isset($options['telegram_chat']) ? $options['telegram_chat'] : '';

        printf(
            '<input type="text" name="%1$s[telegram_chat]" class="regular-text" value="%2$s">',
            $this->option,
            esc_attr($default)
        );

        printf('<p class="description">%s</p>',
            __('Добавьте бота в группу и запросите его состояние: <br>https://api.telegram.org/bot[TOKEN]/getUpdates', 'knife-theme')
        );
    }

    public function setting_render_request_id() {
        $options = get_option($this->option);
        $default = isset($options['request_id']) ? $options['request_id'] : 1;

        printf(
            '<input type="text" name="%1$s[request_id]" class="regular-text" value="%2$s" readonly>',
            $this->option,
            esc_attr($default)
        );
    }


    /**
     * Prints checkbox in post publish action section
     */
    public function print_checkbox() {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'page')
            return;

        $form = get_post_meta($post_id, $this->meta, true);

        printf(
            '<p class="post-attributes-label-wrapper"><span class="post-attributes-label">%s</span></p>',
            __('Дополнительные настройки', 'knife-media')
        );

        printf(
            '<label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label>',
            esc_attr($this->meta),
            __('Добавить форму заявки в клуб', 'knife-theme'),
            checked($form, 1, false)
        );
    }


    /**
     * Save feed post meta
     */
    public function save_meta($post_id) {
        if(get_post_type($post_id) !== 'page')
            return;

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if(!current_user_can('edit_page', $post_id))
            return;

        if(!empty($_REQUEST[$this->meta]))
            update_post_meta($post_id, $this->meta, 1);
        else
            delete_post_meta($post_id, $this->meta);
    }


    /**
     * Append user form to page content
     */
    public function inject_object() {
        if(!is_singular('page'))
            return;

        $post_id = get_the_ID();

        if(!get_post_meta($post_id, $this->meta, true))
            return;

        $fields = [
            'name' => [
                'element' => 'input',
                'type' => 'text',
                'required' => '',
                'autocomplete' => 'name',
                'maxlength' => 50,
                'placeholder' => __('Ваше имя', 'knife-theme'),
            ],

            'email' => [
                'element' => 'input',
                'type' => 'email',
                'required' => '',
                'autocomplete' => 'email',
                'maxlength' => 50,
                'placeholder' => __('Электронная почта', 'knife-theme')
            ],

            'subject' => [
                'element' => 'input',
                'type' => 'text',
                'required' => '',
                'maxlength' => 100,
                'placeholder' => __('О чем хотите писать', 'knife-theme')
            ],

            'text' => [
                'element' => 'textarea',
                'required' => '',
                'placeholder' => __('Текст поста целиком без форматирования', 'knife-theme')
            ]
        ];


        $options = [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'warning' => __('Не удалось отправить форму. Попробуйте еще раз', 'knife-theme'),
            'button' => __('Отправить', 'knife-theme'),
            'action' => $this->action,
            'fields' => $fields,
            'nonce' => wp_create_nonce($this->action)
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_user_form', $options);
    }


    /**
     * Save user form data
     */
    public function submit_form() {
        if(!check_ajax_referer($this->action, 'nonce', false))
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));

        $fields= [];

        foreach(['name', 'email', 'subject', 'text'] as $key) {
            if(empty($_REQUEST[$key]))
                wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));

            $fields[$key] = stripslashes_deep($_REQUEST[$key]);
        }

        $options = get_option($this->option, []);

        if(empty($options['telegram_token']) || empty($options['telegram_chat']))
            wp_send_json_error(__('Ошибка отправки сообщения. Свяжитесь с администратором', 'knife-theme'));


        $request = isset($options['request_id']) ? (int) $options['request_id'] + 1 : 1;

        $message = [
            'chat_id' => $options['telegram_chat'],
            'text' => $this->get_request($fields, $request),
            'parse_mode' => 'HTML'
        ];


        if(!$this->send_telegram($options['telegram_token'], $message))
            wp_send_json_error(__('Ошибка отправки сообщения. Свяжитесь с администратором', 'knife-theme'));


        $options['request_id'] = $request;
        update_option($this->option, $options);

        wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
    }


    /**
     * Create text from array
     */
    private function get_request($fields, $request) {
        $upload = wp_upload_dir();

        $file = sprintf("/requests/%d-%s.html", $request,
            substr(md5(uniqid()), -8)
        );

        $content = $this->create_request($fields, $request);

        if(!file_put_contents($upload['basedir'] . $file, $content))
            wp_send_json_error(__('Не удалось сохранить заявку.', 'knife-theme'));

        $text = sprintf(__('<strong>В клуб добавлена новая заявка #%d</strong>', 'knife-theme'), $request);

        return $text . "\n\n" . esc_url($upload['baseurl'] . $file);
    }


    private function create_request($fields, $request) {
        extract($fields);

        ob_start();

        $include = get_template_directory() . '/core/include';
        include_once($include . '/templates/user-request.php');

        return ob_get_clean();
    }


    /**
     * Send message to Telegram
     */
    private function send_telegram($token, $message) {
        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        $response = curl_exec($ch);

        return json_decode($response)->ok;
    }
}
