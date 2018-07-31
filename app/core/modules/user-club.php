<?php
/**
* User content
*
* Custom ugs club post type
*
* @package knife-theme
* @since 1.3
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}


class Knife_User_Club {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private static
     * @var     string
     */
    private static $slug = 'club';


   /**
    * User form meta
    *
    * @since   1.3
    * @access  private static
    * @var     string
    */
    private static $meta = '_knife-user-form';


   /**
    * Ajax action
    *
    * @since   1.3
    * @access  private static
    * @var     string
    */
    private static $action = 'knife-user-form';


    /**
     * Unique option key to store user settings
     *
     * @since   1.3
     * @access  private static
     * @var     string
     */
    private static $option = 'knife-user-settings';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Apply theme hooks
        add_action('after_setup_theme', [__CLASS__, 'setup_actions']);

        // Register club post type
        add_action('init', [__CLASS__, 'register_club']);

        // Print checkbox user form
        add_action('page_attributes_misc_attributes', [__CLASS__, 'print_checkbox']);

        // Save user form post meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Append user form to content
        add_filter('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Receive user form with ajax
        add_action('wp_ajax_' . self::$action, [__CLASS__, 'submit_request']);
        add_action('wp_ajax_nopriv_' . self::$action, [__CLASS__, 'submit_request']);

        // Add settings page
        add_action('admin_init', [__CLASS__, 'settings_init']);
        add_action('admin_menu', [__CLASS__, 'add_menu']);

        // Handle create role settings link
        add_action('load-settings_page_knife-club', [__CLASS__, 'create_role']);

        // Add post archive description
        add_filter('get_the_archive_description', [__CLASS__, 'add_description'], 9);

        // Notify on sendig to review
        add_action('draft_to_pending', [__CLASS__, 'notify_review']);
        add_action('auto-draft_to_pending', [__CLASS__, 'notify_review']);

        // Add user post type to author archive
        add_action('pre_get_posts', [__CLASS__, 'update_archive'], 12);

        // Prepend author meta to content
        add_filter('the_content', [__CLASS__, 'insert_metalink']);

        // Append promo link to club content
        add_filter('the_content', [__CLASS__, 'insert_club_promo']);

        // Append promo link to standart post content
        add_filter('the_content', [__CLASS__, 'insert_post_promo']);
    }


    /**
     * Setup theme hooks
     *
     * @since 1.4
     */
    public static function setup_actions() {
        // Add post lead to post type editor
        add_filter('knife_post_lead_type', function($types) {
            $types[] = self::$slug;

            return $types;
        });

        // Add push service club post type
        add_filter('knife_push_service_type', function($types) {
            $types[] = self::$slug;

            return $types;
        });
    }


    /**
     * Create new user role
     */
    public static function create_role() {
        if(!isset($_GET['_wpnonce'], $_GET['action'])) {
            return;
        }

        $action = $_GET['action'];

        if($action === 'create-role' && wp_verify_nonce($_GET['_wpnonce'], $action)) {
            $roles = [
                'administrator' => true,
                'editor' => true,
                'club_user' => false
            ];

            add_role('club_user', __('Участник клуба', 'knife-theme'), [
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => false,
            ]);


            foreach($roles as $name => $can_edit) {
                if(!$role = get_role($name))
                    continue;

                $role->add_cap('read');
                $role->add_cap('read_club_item');
                $role->add_cap('edit_club_item');
                $role->add_cap('edit_club_items');

                if($can_edit === false)
                    continue;

                $role->add_cap('read_private static_club_items');
                $role->add_cap('edit_others_club_items');
                $role->add_cap('edit_published_club_items');
                $role->add_cap('edit_private static_club_items');
                $role->add_cap('publish_club_items');
                $role->add_cap('delete_club_items');
                $role->add_cap('delete_others_club_items');
                $role->add_cap('delete_private static_club_items');
                $role->add_cap('delete_published_club_items');
            }


            if(get_role('contributor')) {
                remove_role('contributor');
            }
        }

        wp_safe_redirect(admin_url('options-general.php?page=knife-club'));
        exit;
    }


    /**
     * Register club post type
     */
    public static function register_club() {
        register_post_type(self::$slug, [
            'labels'                => [
                'name'              => __('Клуб', 'knife-theme'),
                'singular_name'     => __('Запись в клуб', 'knife-theme'),
                'add_new'           => __('Добавить запись', 'knife-theme'),
                'menu_name'         => __('Клуб', 'knife-theme')
            ],
            'label'                 => __('Клуб', 'knife-theme'),
            'description'           => __('Клуб — это сообщество друзей «Ножа», <br>которым есть что сказать.', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'editor', 'excerpt', 'author'],
            'hierarchical'          => false,
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
     * Add button to description
     */
    public static function add_description($description) {
        $options = get_option(self::$option);

        if(get_post_type() !== self::$slug) {
            return $description;
        }

        if(empty($options['button_link'])) {
            return $description;
        }

        $button = sprintf('<a class="button bright" href="%2$s">%1$s</a>',
            __('Присоединиться', 'knife-theme'),
            esc_url($options['button_link'])
        );

        return $description . $button;
    }


    /**
     * Insert link to author on single club post
     */
    public static function insert_metalink($content) {
        if(!is_singular(self::$slug) || !in_the_loop()) {
            return $content;
        }

        $meta = get_the_author_meta('ID');

        $link = sprintf('<a class="outbound" href="%3$s"><p class="outbound__author">%1$s</p><p>%2$s</p></a>',
            get_the_author(),
            get_the_author_meta('description'),
            get_author_posts_url($meta)
        );

        return $link . $content;
    }


    /**
     * Insert user club promo link to content
     *
     * @since 1.4
     */
    public static function insert_post_promo($content) {
        if(!is_singular('post') || !in_the_loop()) {
            return $content;
        }

        if(get_post_format() !== false) {
            return $content;
        }

        $options = get_option(self::$option);

        if(empty($options['button_link'])) {
            return $content;
        }

        $link = sprintf('<a class="outbound outbound--footer" href="%2$s"><p class="outbound__promo">%1$s</p></a>',
            __('Хотите тоже написать что-то интересное в «Нож», но у вас мало опыта? Это не страшно: присоединяйтесь к нашему Клубу! Там мы публикуем тексты читателей, а лучшим предлагаем стать нашими постоянными авторами.', 'knife-theme'),
            esc_url($options['button_link'])
        );

        return $content . $link;
    }


    /**
     * Insert user club promo link to content
     *
     * @since 1.4
     */
    public static function insert_club_promo($content) {
        if(!is_singular(self::$slug) || !in_the_loop()) {
            return $content;
        }

        $options = get_option(self::$option);

        if(empty($options['button_link'])) {
            return $content;
        }

        $link = sprintf('<a class="outbound outbound--footer" href="%2$s"><p class="outbound__promo">%1$s</p></a>',
            __('Вы тоже можете писать в Клуб «Ножа»!<br> Попробуйте, это бесплатно и совершенно не страшно.', 'knife-theme'),
            esc_url($options['button_link'])
        );

        return $content . $link;
    }


    /**
     * Append to author archive loop club posts
     */
    public static function update_archive($query) {
        if(is_author() && $query->is_main_query()) {
            $types = $query->get('post_type');

            if(!is_array($types)) {
                $types = ['post'];
            }

            $types[] = self::$slug;

            $query->set('post_type', $types);
        }
    }


    /**
     * Add club settings submenu to main options menu
     */
    public static function add_menu() {
        add_submenu_page('options-general.php', __('Настройки клуба', 'knife-theme'), __('Настройки клуба', 'knife-theme'), 'manage_options', 'knife-club', [__CLASS__, 'settings_page']);
    }


    /**
     * Display push options page
     */
    public static function settings_page() {
        echo '<form class="wrap" action="options.php" method="post">';

        settings_fields('knife-user-settings');
        do_settings_sections('knife-user-settings');

        $reset = sprintf('<a href="%2$s" class="button">%1$s</a>',
            __('Сбросить роли пользователей', 'knife-theme'),
            wp_nonce_url(admin_url(add_query_arg('action', 'create-role', 'options-general.php?page=knife-club')), 'create-role')
        );

        printf('<p>%1$s &nbsp; %2$s</p>',
            get_submit_button(__('Сохранить настройки'), 'primary', 'submit', false), $reset
        );

        echo '</form>';
    }


    /**
     * Register settings forms
     */
    public static function settings_init() {
        register_setting('knife-user-settings', self::$option);

        add_settings_section(
            'knife-user-section',
            __('Настройки клуба', 'knife-theme'),
            [],
            'knife-user-settings'
        );

        add_settings_field(
            'telegram_token',
            __('Telegram Access Token', 'knife-theme'),
            [__CLASS__, 'setting_render_telegram_token'],
            'knife-user-settings',
            'knife-user-section'
        );

        add_settings_field(
            'telegram_chat',
            __('ID чата администраторов в Telegram', 'knife-theme'),
            [__CLASS__, 'setting_render_telegram_chat'],
            'knife-user-settings',
            'knife-user-section'
        );

        add_settings_field(
            'button_link',
            __('Ссылка с кнопки в архиве', 'knife-theme'),
            [__CLASS__, 'setting_render_button_link'],
            'knife-user-settings',
            'knife-user-section'
        );


        add_settings_field(
            'request_id',
            __('ID последней заявки', 'knife-theme'),
            [__CLASS__, 'setting_render_request_id'],
            'knife-user-settings',
            'knife-user-section'
        );
    }

    public static function setting_render_telegram_token() {
        $options = get_option(self::$option);
        $default = isset($options['telegram_token']) ? $options['telegram_token'] : '';

        printf(
            '<input type="text" name="%1$s[telegram_token]" class="regular-text" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_telegram_chat() {
        $options = get_option(self::$option);
        $default = isset($options['telegram_chat']) ? $options['telegram_chat'] : '';

        printf(
            '<input type="text" name="%1$s[telegram_chat]" class="regular-text" value="%2$s">',
            self::$option,
            esc_attr($default)
        );

        printf('<p class="description">%s</p>',
            __('Добавьте бота в группу и запросите его состояние: <br>https://api.telegram.org/bot[TOKEN]/getUpdates', 'knife-theme')
        );
    }

    public static function setting_render_button_link() {
        $options = get_option(self::$option);
        $default = isset($options['button_link']) ? $options['button_link'] : '';

        printf(
            '<input type="text" name="%1$s[button_link]" class="regular-text" value="%2$s">',
            self::$option,
            esc_attr($default)
        );
    }

    public static function setting_render_request_id() {
        $options = get_option(self::$option);
        $default = isset($options['request_id']) ? $options['request_id'] : 1;

        printf(
            '<input type="text" name="%1$s[request_id]" class="regular-text" value="%2$s">',
            self::$option,
            esc_attr($default)
        );

        printf('<p class="description">%s</p>',
            __('Для сброса установите нулевое значение', 'knife-theme')
        );
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox() {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== 'page') {
            return;
        }

        $form = get_post_meta($post_id, self::$meta, true);

        printf(
            '<p class="post-attributes-label-wrapper"><span class="post-attributes-label">%s</span></p>',
            __('Дополнительные настройки', 'knife-media')
        );

        printf(
            '<label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label>',
            esc_attr(self::$meta),
            __('Добавить форму заявки в клуб', 'knife-theme'),
            checked($form, 1, false)
        );
    }


    /**
     * Save feed post meta
     */
    public static function save_meta($post_id) {
        if(get_post_type($post_id) !== 'page') {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_page', $post_id)) {
            return;
        }

        if(!empty($_REQUEST[self::$meta]))
            update_post_meta($post_id, self::$meta, 1);
        else
            delete_post_meta($post_id, self::$meta);
    }


    /**
     * Append user form to page content
     */
    public static function inject_object() {
        if(!is_singular('page')) {
            return;
        }

        $post_id = get_the_ID();

        if(!get_post_meta($post_id, self::$meta, true)) {
            return;
        }

        $fields = [
            'name' => [
                'element' => 'input',
                'type' => 'text',
                'required' => '',
                'autocomplete' => 'name',
                'maxlength' => 50,
                'placeholder' => __('Ваше имя, род занятий и проекты', 'knife-theme'),
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
            'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
            'warning' => __('Не удалось отправить форму. Попробуйте еще раз', 'knife-theme'),
            'button' => __('Отправить', 'knife-theme'),
            'action' => self::$action,
            'fields' => $fields,
            'nonce' => wp_create_nonce(self::$action)
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_user_form', $options);
    }


    /**
     * Notify on sending to review
     */
    public static function notify_review($post) {
        $options = get_option(self::$option, []);

        $message = [
            'chat_id' => $options['telegram_chat'],
            'text' => self::get_review($post),
            'parse_mode' => 'HTML'
        ];


        return self::send_telegram($options['telegram_token'], $message);
    }


    /**
     * Save user form data
     */
    public static function submit_request() {
        if(!check_ajax_referer(self::$action, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }

        $fields= [];

        foreach(['name', 'email', 'subject', 'text'] as $key) {
            if(empty($_REQUEST[$key])) {
                wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));
            }

            $fields[$key] = stripslashes_deep($_REQUEST[$key]);
        }

        $options = get_option(self::$option, []);

        if(empty($options['telegram_token']) || empty($options['telegram_chat'])) {
            wp_send_json_error(__('Ошибка отправки сообщения. Свяжитесь с администратором', 'knife-theme'));
        }


        $request = isset($options['request_id']) ? (int) $options['request_id'] + 1 : 1;

        $message = [
            'chat_id' => $options['telegram_chat'],
            'text' => self::get_request($fields, $request),
            'parse_mode' => 'HTML'
        ];


        if(!self::send_telegram($options['telegram_token'], $message)) {
            wp_send_json_error(__('Ошибка отправки сообщения. Свяжитесь с администратором', 'knife-theme'));
        }


        $options['request_id'] = $request;
        update_option(self::$option, $options);

        wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
    }


    /**
     * Create text from array
     */
    private static function get_request($fields, $request) {
        $upload = wp_upload_dir();
        $folder = '/requests/';

        $file = sprintf("%d-%s.html", $request,
            substr(md5(uniqid()), -8)
        );

        $path = $folder . $file;

        if(!is_dir($upload['basedir'] . $folder) && !mkdir($upload['basedir'] . $folder)) {
            wp_send_json_error(__('Не удалось сохранить заявку.', 'knife-theme'));
        }

        $content = self::create_request($fields, $request);

        if(!file_put_contents($upload['basedir'] . $path, $content)) {
            wp_send_json_error(__('Не удалось сохранить заявку.', 'knife-theme'));
        }

        $text = sprintf("%s\n\n%s \n%s \n\n%s",
            sprintf(__('<strong>В клуб добавлена новая заявка #%d</strong>', 'knife-theme'), $request),
            sprintf(__('Автор: %s', 'knife-theme'), esc_attr($fields['name'])),
            sprintf(__('Тема: %s', 'knife-theme'), esc_attr($fields['subject'])),
            esc_url($upload['baseurl'] . $path)
        );

        return $text;
    }


    /**
     * Get review message for telegram bot
     */
    private static function get_review($post) {
        $author = get_userdata($post->post_author);

        $text = sprintf("%s\n\n%s \n%s \n\n%s",
            __('<strong>В клуб добавлена новая запись на утверждение</strong>', 'knife-theme'),
            sprintf(__('Автор: %s', 'knife-theme'), esc_attr($author->display_name)),
            sprintf(__('Тема: %s', 'knife-theme'), esc_attr($post->post_title)),
            esc_url(get_preview_post_link($post))
        );

        return $text;
    }


    /**
     * Create request byt template
     */
    private static function create_request($fields, $request) {
        extract($fields);

        ob_start();

        $include = get_template_directory() . '/core/include';
        include_once($include . '/templates/user-request.php');

        return ob_get_clean();
    }


    /**
     * Send message to Telegram
     */
    private static function send_telegram($token, $message) {
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


/**
 * Load module
 */
Knife_User_Club::load_module();
