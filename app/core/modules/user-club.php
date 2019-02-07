<?php
/**
* User content
*
* Custom ugs club post type
*
* @package knife-theme
* @since 1.3
* @version 1.7
*/


if (!defined('WPINC')) {
    die;
}


class Knife_User_Club {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $slug = 'club';


   /**
    * User form meta
    *
    * @since   1.3
    * @access  private
    * @var     string
    */
    private static $meta = '_knife-user-form';


   /**
    * Ajax action
    *
    * @since   1.3
    * @access  private
    * @var     string
    */
    private static $action = 'knife-user-form';


    /**
     * Unique option key to store telegram chat id
     *
     * @since   1.7
     * @access  private
     * @var     string
     */
    private static $telegram_chat = 'knife_club_telegram_chat';


    /**
     * Unique option key to store club button link
     *
     * @since   1.7
     * @access  private
     * @var     string
     */
    private static $button_link = 'knife_club_button_link';


    /**
     * Unique option key to store current request id
     *
     * @since   1.7
     * @access  private
     * @var     string
     */
    private static $request_id = 'knife_club_request_id';


    /**
     * Checkbox save nonce
     *
     * @since   1.5
     * @access  private
     * @var     string
     */
    private static $nonce = 'knife-user-form-nonce';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
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

        // Add club settings to customizer
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);

        // Handle create role settings link
        add_action('after_switch_theme', [__CLASS__, 'create_role']);

        // Update archive caption description
        add_filter('get_the_archive_description', [__CLASS__, 'update_archive_description'], 12);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Notify on sendig to review
        add_action('draft_to_pending', [__CLASS__, 'notify_review']);
        add_action('auto-draft_to_pending', [__CLASS__, 'notify_review']);

        // Add club post type to archives
        add_action('pre_get_posts', [__CLASS__, 'update_archives'], 12);

        // Prepend author meta to content
        add_filter('the_content', [__CLASS__, 'insert_author_link']);

        // Append promo link to club content
        add_filter('the_content', [__CLASS__, 'insert_club_promo']);
    }


    /**
     * Create new user role
     */
    public static function create_role() {
        if(get_role('club_user') === null) {
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
                if(!$role = get_role($name)) {
                    continue;
                }

                $role->add_cap('read');
                $role->add_cap('read_club_item');
                $role->add_cap('edit_club_item');
                $role->add_cap('edit_club_items');

                if($can_edit === false) {
                    continue;
                }

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
        }
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
            'supports'              => ['title', 'thumbnail', 'revisions', 'editor', 'excerpt', 'author', 'comments'],
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
            'taxonomies'            => ['post_tag'],
            'capability_type'       => ['club_item', 'club_items'],
            'map_meta_cap'          => true
        ]);
    }


    /**
     * Add button to description
     */
    public static function update_archive_description($description) {
        $button_link = get_theme_mod(self::$button_link, '');

        if(is_post_type_archive(self::$slug) && strlen($button_link) > 0) {
            $button = sprintf('<div class="tagline-button tagline-button--club"><a class="button" href="%2$s">%1$s</a></div>',
                __('Присоединиться', 'knife-theme'),
                esc_url($button_link)
            );

            $description = $description . $button;
        }

        return $description;
    }


    /**
     * Update post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title($title) {
        if(is_post_type_archive(self::$slug)) {
            $title = sprintf('<h1 class="tagline-title tagline-title--club">%s</h1>',
                post_type_archive_title('', false)
            );
        }

        return $title;
    }


    /**
     * Insert link to author on single club post
     *
     * @version 1.4
     */
    public static function insert_author_link($content) {
        if(!is_singular(self::$slug) || !in_the_loop()) {
            return $content;
        }

        if($custom_url = get_the_author_meta('user_url')) {
            $author_url = sprintf('<a class="author author--club" href="%3$s" target="_blank"><strong>%1$s </strong>%2$s</a>',
                get_the_author(),
                esc_html(get_the_author_meta('description')),
                esc_url($custom_url)
            );

            return $author_url . $content;
        }

        $author_url = sprintf('<a class="author author--club" href="%3$s"><strong>%1$s </strong>%2$s</a>',
            get_the_author(),
            esc_html(get_the_author_meta('description')),
            esc_url(get_author_posts_url(get_the_author_meta('ID')))
        );

        return $author_url . $content;
    }


    /**
     * Insert user club promo link to content
     *
     * @since 1.4
     */
    public static function insert_club_promo($content) {
        if(is_singular(self::$slug) && in_the_loop()) {
            $button_link = get_theme_mod(self::$button_link, '');

            if(strlen($button_link) > 0) {
                $promo_link = sprintf('<a class="promo promo--club" href="%2$s">%1$s</a>',
                    __('Вы тоже можете писать в Клуб «Ножа»!<br> Попробуйте, это бесплатно и совершенно не страшно.', 'knife-theme'),
                    esc_url($button_link)
                );

                $content = $content . $promo_link;
            }
        }

        return $content;
    }


    /**
     * Append club posts to author and tag archives
     */
    public static function update_archives($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        if($query->is_tag() || $query->is_author()) {
            $types = $query->get('post_type');

            if(!is_array($types)) {
                $types = ['post'];
            }

            $types[] = self::$slug;

            $query->set('post_type', $types);
        }
    }


    /**
     * Add club settings to customizer
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_section('knife_club', [
            'title' => __('Настройки клуба','knife-theme'),
            'priority' => 200,
        ]);

        $wp_customize->add_setting(self::$telegram_chat);
        $wp_customize->add_setting(self::$request_id);
        $wp_customize->add_setting(self::$button_link);


        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$telegram_chat, [
                 'label' => __('ID чата в Telegram', 'knife-theme'),
                 'section' => 'knife_club'
             ]
        ));

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$button_link, [
                 'label' => __('Ссылка с кнопки в архиве', 'knife-theme'),
                 'section' => 'knife_club'
             ]
        ));

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$request_id, [
                 'label' => __('ID последней заявки', 'knife-theme'),
                 'section' => 'knife_club'
             ]
        ));
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

        wp_nonce_field('checkbox', self::$nonce);
    }


    /**
     * Save feed post meta
     */
    public static function save_meta($post_id) {
        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'checkbox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_page', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta])) {
            return delete_post_meta($post_id, self::$meta);
        }

        return update_post_meta($post_id, self::$meta, 1);
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
        $chat_id = get_theme_mod(self::$telegram_chat, '');

        $message = [
            'chat_id' => $chat_id,
            'text' => self::get_review($post),
            'parse_mode' => 'HTML'
        ];

        if(method_exists('Knife_Notifier_Robot', 'send_telegram')) {
            Knife_Notifier_Robot::send_telegram($message);
        }
    }


    /**
     * Send user form data
     */
    public static function submit_request() {
        if(!check_ajax_referer(self::$action, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }

        $fields = [];

        foreach(['name', 'email', 'subject', 'text'] as $key) {
            if(empty($_REQUEST[$key])) {
                wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));
            }

            $fields[$key] = stripslashes_deep($_REQUEST[$key]);
        }


        if(method_exists('Knife_Notifier_Robot', 'send_telegram')) {
            $chat_id = get_theme_mod(self::$telegram_chat, '');
            $request = get_theme_mod(self::$request_id, 0) + 1;

            $message = [
                'chat_id' => $chat_id,
                'text' => self::get_request($fields, $request),
                'parse_mode' => 'HTML'
            ];

            if(Knife_Notifier_Robot::send_telegram($message)) {
                set_theme_mod(self::$request_id, $request);
                wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
            }
        }

        wp_send_json_error(__('Ошибка отправки сообщения. Попробуйте позже', 'knife-theme'));
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
     * Create request by template
     */
    private static function create_request($fields, $request) {
        extract($fields);

        ob_start();

        $include = get_template_directory() . '/core/include';
        include_once($include . '/templates/user-request.php');

        return ob_get_clean();
    }
}


/**
 * Load module
 */
Knife_User_Club::load_module();
