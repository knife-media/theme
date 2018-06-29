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
        // print checkbox user form
        add_action('page_attributes_misc_attributes', [$this, 'print_checkbox']);

        // save user form post meta
        add_action('save_post', [$this, 'save_meta']);

        // append user form to content
        add_filter('wp_enqueue_scripts', [$this, 'inject_object'], 12);

        // receive user form with ajax
        add_action('wp_ajax_' . $this->action, [$this, 'submit_request']);
        add_action('wp_ajax_nopriv_' . $this->action, [$this, 'submit_request']);

        // add settings page
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_menu', [$this, 'add_menu']);

        // handle create role settings link
        add_action('load-settings_page_knife-club', [$this, 'create_role']);

        // register club post type
        add_action('init', [$this, 'register_club']);

        // add post archive description
        add_filter('get_the_archive_description', [$this, 'add_description'], 9);

        // notify on sendig to review
        add_action('draft_to_pending', [$this, 'notify_review']);
        add_action('auto-draft_to_pending', [$this, 'notify_review']);

        // add user post type to author archive
        add_action('pre_get_posts', [$this, 'update_archive'], 12);

        // add author meta to content
        add_filter('the_content', [$this, 'insert_metalink']);
    }


    /**
     * Create new user role
     */
    public function create_role() {
        if(!isset($_GET['_wpnonce'], $_GET['action']))
            return;

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

                $role->add_cap('read_private_club_items');
                $role->add_cap('edit_others_club_items');
                $role->add_cap('edit_published_club_items');
                $role->add_cap('edit_private_club_items');
                $role->add_cap('publish_club_items');
                $role->add_cap('delete_club_items');
                $role->add_cap('delete_others_club_items');
                $role->add_cap('delete_private_club_items');
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
    public function register_club() {
        register_post_type($this->slug, [
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
    public function add_description($description) {
        $options = get_option($this->option);

        if(get_post_type() !== $this->slug)
            return $description;

        if(empty($options['button_link']))
            return $description;

        $button = sprintf('<a class="button bright" href="%2$s">%1$s</a>',
            __('Присоединиться', 'knife-theme'),
            esc_url($options['button_link'])
        );

        return $description . $button;
    }


    /**
     * Insert link to author on single club post
     */
    public function insert_metalink($content) {
        if(get_post_type() !== $this->slug)
            return $content;

        if(!is_singular($this->slug) || !in_the_loop())
            return $content;

        $meta = get_the_author_meta('ID');

        $link = sprintf('<a class="profile" href="%3$s"><h3>%1$s</h3><p>%2$s</p></a>',
            get_the_author(),
            get_the_author_meta('description'),
            get_author_posts_url($meta)
        );

        return $link . $content;
    }


    /**
     * Append to author archive loop club posts
     */
    public function update_archive($query) {
        if(is_author() && $query->is_main_query()) {
            $types = $query->get('post_type');
            $set = ['post'];

            if(is_array($types))
                $set = explode(',', $types);

            $set[] = $this->slug;

            $query->set('post_type', $set);
        }
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
    public function settings_init() {
        register_setting('knife-user-settings', $this->option);

        add_settings_section(
            'knife-user-section',
            __('Настройки клуба', 'knife-theme'),
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
            'button_link',
            __('Ссылка с кнопки в архиве', 'knife-theme'),
            [$this, 'setting_render_button_link'],
            'knife-user-settings',
            'knife-user-section'
        );


        add_settings_field(
            'request_id',
            __('ID последней заявки', 'knife-theme'),
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

    public function setting_render_button_link() {
        $options = get_option($this->option);
        $default = isset($options['button_link']) ? $options['button_link'] : '';

        printf(
            '<input type="text" name="%1$s[button_link]" class="regular-text" value="%2$s">',
            $this->option,
            esc_attr($default)
        );
    }

    public function setting_render_request_id() {
        $options = get_option($this->option);
        $default = isset($options['request_id']) ? $options['request_id'] : 1;

        printf(
            '<input type="text" name="%1$s[request_id]" class="regular-text" value="%2$s">',
            $this->option,
            esc_attr($default)
        );

        printf('<p class="description">%s</p>',
            __('Для сброса установите нулевое значение', 'knife-theme')
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
            'action' => $this->action,
            'fields' => $fields,
            'nonce' => wp_create_nonce($this->action)
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_user_form', $options);
    }


    /**
     * Notify on sending to review
     */
    public function notify_review($post) {
        $options = get_option($this->option, []);

        $message = [
            'chat_id' => $options['telegram_chat'],
            'text' => $this->get_review($post),
            'parse_mode' => 'HTML'
        ];


        return $this->send_telegram($options['telegram_token'], $message);
    }


    /**
     * Save user form data
     */
    public function submit_request() {
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
        $folder = '/requests/';

        $file = sprintf("%d-%s.html", $request,
            substr(md5(uniqid()), -8)
        );

        $path = $folder . $file;

        if(!is_dir($upload['basedir'] . $folder) && !mkdir($upload['basedir'] . $folder))
            wp_send_json_error(__('Не удалось сохранить заявку.', 'knife-theme'));

        $content = $this->create_request($fields, $request);

        if(!file_put_contents($upload['basedir'] . $path, $content))
            wp_send_json_error(__('Не удалось сохранить заявку.', 'knife-theme'));

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
    private function get_review($post) {
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
