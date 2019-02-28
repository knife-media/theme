<?php
/**
 * Ask post type
 *
 * Custom post type for user answers and questions
 *
 * @package knife-theme
 * @since 1.7
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Ask_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $post_type = 'ask';


    /**
     * Unique meta to store ask options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-ask-options';


    /**
     * Current front question counter option
     * This meta stores in wp_options table
     *
     * @access  private
     * @var     string
     */
    private static $option_counter = 'knife-ask-counter';


    /**
     * Unique option key to store telegram chat id
     *
     * @access  private
     * @var     string
     */
    private static $option_chat = 'knife_ask_telegram_chat';


    /**
     * Unique option key to store current question id
     *
     * @access  private
     * @var     string
     */
    private static $option_request = 'knife_ask_request_id';


    /**
     * Unique option key to store default ask post thumbnail
     *
     * @access  private
     * @var     string
     */
    private static $option_thumbnail = 'knife_ask_thumbnail';


    /**
     * Unique nonce string
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-ask-nonce';


   /**
    * Send form ajax action
    *
    * @access  private
    * @var     string
    */
    private static $ajax_request = 'knife-ask-request';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register ask post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add ask author metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Set default thumbnail on save post
        add_action('save_post', [__CLASS__, 'set_thumbnail']);

        // Append ask form to content
        add_filter('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Send ask form with ajax
        add_action('wp_ajax_' . self::$ajax_request, [__CLASS__, 'submit_request']);
        add_action('wp_ajax_nopriv_' . self::$ajax_request, [__CLASS__, 'submit_request']);

        // Add ask settings to customizer
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);

        // Include ask single template
        add_action('single_template', [__CLASS__, 'include_single']);

        // Include ask archive template
        add_filter('archive_template', [__CLASS__, 'include_archive']);

        // Don't show empty archive
        add_action('template_redirect', [__CLASS__, 'redirect_empty_archive']);

        // Change posts count on ask archive
        add_action('pre_get_posts', [__CLASS__, 'update_count']);

        // Add ask post type to archives
        add_action('pre_get_posts', [__CLASS__, 'update_archives'], 12);

        // Prepend author meta to content
        add_filter('the_content', [__CLASS__, 'insert_author_link']);
    }


    /**
     * Register ask post type
     */
    public static function register_type() {
        register_post_type(self::$post_type, [
            'labels'                    => [
                'name'                  => __('Вопросы', 'knife-theme'),
                'singular_name'         => __('Вопрос', 'knife-theme'),
                'add_new'               => __('Добавить вопрос', 'knife-theme'),
                'menu_name'             => __('Вопросы', 'knife-theme'),
                'all_items'             => __('Все вопросы', 'knife-theme'),
                'add_new_item'          => __('Добавить новый вопрос', 'knife-theme'),
                'new_item'              => __('Новый вопрос', 'knife-theme'),
                'edit_item'             => __('Редактировать вопрос', 'knife-theme'),
                'update_item'           => __('Обновить вопрос', 'knife-theme'),
                'view_item'             => __('Просмотреть вопрос', 'knife-theme'),
                'view_items'            => __('Просмотреть вопросы', 'knife-theme'),
                'search_items'          => __('Искать вопрос', 'knife-theme'),
                'insert_into_item'      => __('Добавить в вопрос', 'knife-theme')
            ],
            'label'                 => __('Вопросы', 'knife-theme'),
            'description'           => __('Раздел с вопросами пользователей', 'knife-theme'),
            'supports'              => ['title', 'editor', 'revisions', 'excerpt', 'author', 'comments'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 9,
            'menu_icon'             => 'dashicons-editor-help',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'taxonomies'            => ['post_tag']
        ]);
    }


    /**
     * Add question settings to customizer
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_section('knife_ask', [
            'title' => __('Настройки вопросов','knife-theme'),
            'priority' => 250,
        ]);

        // Chat setting
        $wp_customize->add_setting(self::$option_chat);

        // Last request id setting
        $wp_customize->add_setting(self::$option_request);

        // Thumbnail setting
        $wp_customize->add_setting(self::$option_thumbnail);


        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$option_chat, [
                 'label' => __('ID чата в Telegram', 'knife-theme'),
                 'section' => 'knife_ask'
             ]
        ));

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$option_request, [
                 'label' => __('ID последнего вопроса', 'knife-theme'),
                 'section' => 'knife_ask'
             ]
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            self::$option_thumbnail, [
                'label' => __('Обложка по умолчанию', 'knife-theme'),
                'section' => 'knife_ask'
            ]
        ));
    }


    /**
     * Add ask author metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-ask-metabox', __('Настройка вопроса'), [__CLASS__, 'display_metabox'], self::$post_type, 'side', 'high');
    }


    /**
     * Display ask author metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/ask-metabox.php');
    }


    /**
     * Save ask author metabox
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$post_type) {
            return;
        }

        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            $options = $_REQUEST[self::$meta_options];

            if(isset($options['counter'])) {
                update_option(self::$option_counter, $options['counter']);
            }

            update_post_meta($post_id, self::$meta_options, $options);
        }
    }


    /**
     * Set default thumbnail on save post
     */
    public static function set_thumbnail($post_id) {
        if(get_post_type($post_id) !== self::$post_type) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        $thumbnail_id = get_theme_mod(self::$option_thumbnail, '');

        if($thumbnail_id) {
            update_post_meta($post_id, '_thumbnail_id', $thumbnail_id);
        }
    }


    /**
     * Append user form to page content
     */
    public static function inject_object() {
        if(is_singular(self::$post_type)) {
            $fields = [
                'text' => [
                    'element' => 'textarea',
                    'required' => '',
                    'placeholder' => __('А вдруг квантовая физика — выдумка гуманитарев?', 'knife-theme')
                ],

                'name' => [
                    'element' => 'input',
                    'type' => 'text',
                    'required' => '',
                    'autocomplete' => 'name',
                    'placeholder' => __('Как вас зовут?', 'knife-theme'),
                ],

                'contact' => [
                    'element' => 'input',
                    'type' => 'text',
                    'required' => '',
                    'placeholder' => __('Как с вами связаться?', 'knife-theme')
                ]
            ];

            $options = [
                'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
                'warning' => __('Не удалось отправить вопрос', 'knife-theme'),
                'button' => __('Спросить у специалиста', 'knife-theme'),
                'heading' => __('Вы можете задать свой вопрос журналу «Нож»', 'knife-theme'),
                'action' => self::$ajax_request,
                'fields' => $fields,
                'styles' => esc_attr('form--ask'),
                'nonce' => wp_create_nonce(self::$ajax_request)
            ];

            // add ask form fields
            wp_localize_script('knife-theme', 'knife_form_request', $options);
        }
    }


    /**
     * Send user form data
     */
    public static function submit_request() {
        if(!check_ajax_referer(self::$ajax_request, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }

        $fields = [];

        foreach(['name', 'text', 'contact'] as $key) {
            if(empty($_REQUEST[$key])) {
                wp_send_json_error(__('Необходимо заполнить все поля формы', 'knife-theme'));
            }

            $fields[$key] = stripslashes_deep($_REQUEST[$key]);
        }


        if(method_exists('Knife_Notifier_Robot', 'send_telegram')) {
            $chat_id = get_theme_mod(self::$option_chat, '');
            $request = get_theme_mod(self::$option_request, 0) + 1;

            $message = [
                'chat_id' => $chat_id,
                'text' => self::get_request($fields, $request),
                'parse_mode' => 'HTML'
            ];

            if(Knife_Notifier_Robot::send_telegram($message)) {
                set_theme_mod(self::$option_request, $request);
                wp_send_json_success(__('Сообщение успешно отправлено', 'knife-theme'));
            }
        }

        wp_send_json_error(__('Ошибка отправки сообщения', 'knife-theme'));
    }


    /**
     * Create text from array
     */
    private static function get_request($fields, $request) {
        $upload = wp_upload_dir();
        $folder = '/questions/';

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
            sprintf(__('<strong>Добавлен новый вопрос #%d</strong>', 'knife-theme'), $request),
            sprintf(__('Автор вопроса: %s', 'knife-theme'), esc_attr($fields['name'])),
            sprintf(__('Контакты автора: %s', 'knife-theme'), esc_attr($fields['contact'])),
            esc_url($upload['baseurl'] . $path)
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
        include_once($include . '/templates/ask-request.php');

        return ob_get_clean();
    }


    /**
     * Insert link to author on single ask post
     */
    public static function insert_author_link($content) {
        if(!is_singular(self::$post_type) || !in_the_loop()) {
            return $content;
        }

        $author = [];

        // Allowed description tags
        $allowed = array(
            'a' => [
                'href' => true,
                'target' => true,
            ]
        );

        // Get current post author id
        $user_id = get_the_author_meta('ID');

        // Add author name with link
        $author[] = sprintf(
            '<div class="author__name"><strong>%s</strong>%s</div>',

            sprintf(
                __('Отвечает <a href="%1$s">%2$s</a>', 'knife-theme'),
                esc_url(get_author_posts_url($user_id)),
                esc_html(get_the_author())
            ),

            sprintf(
                '<p class="author__description">%s</p>',
                wp_kses(get_the_author_meta('description'), $allowed)
            )
        );

        // Add photo if exists
        $photo = get_user_meta($user_id, '_knife-user-photo', true);

        if(strlen($photo) > 0) {
            $author[] = sprintf(
                '<img class="author__photo" src="%2$s" alt="%1$s">',
                esc_html(get_the_author()),
                esc_url($photo)
            );
        }

        $output = sprintf(
            '<div class="author author--ask">%s</div>',
            implode("\n", $author)
        );

        return $output . $content;
    }


    /**
     * Include archive ask template
     */
    public static function include_archive($template) {
        if(is_post_type_archive(self::$post_type)) {
            $new_template = locate_template(['templates/archive-ask.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Include single ask template
     */
    public static function include_single($template) {
        if(is_singular(self::$post_type)) {
            $new_template = locate_template(['templates/single-ask.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Redirect empty archive
     */
    public static function redirect_empty_archive() {
        if(is_post_type_archive(self::$post_type) && !have_posts()) {
            wp_redirect(home_url(), 302);
            exit;
        }
    }


    /**
     * Change posts_per_page for ask archive template
     */
    public static function update_count($query) {
        if($query->is_main_query() && $query->is_post_type_archive(self::$post_type)) {
            $query->set('posts_per_page', 16);
        }
    }


    /**
     * Append ask posts to author and tag archives
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

            $types[] = self::$post_type;

            $query->set('post_type', $types);
        }
    }
}


/**
 * Load current module environment
 */
Knife_Ask_Section::load_module();
