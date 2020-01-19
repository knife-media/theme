<?php
/**
* User content
*
* Custom ugs club post type
*
* @package knife-theme
* @since 1.3
* @version 1.12
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Club_Section {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  public
     * @var     string
     */
    public static $post_type = 'club';


   /**
    * Club form meta
    *
    * @access  private
    * @var     string
    */
    private static $meta_form = '_knife-club-form';


    /**
     * Unique option key to store current request id
     *
     * @access  private
     * @var     string
     * @since   1.7
     */
    private static $option_request = 'knife_club_request_id';


    /**
     * Ajax action
     *
     * @access  private
     * @var     string
     * @since   1.7
     */
    private static $ajax_request = 'knife-club-request';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register club post type
        add_action('init', [__CLASS__, 'register_type']);

        // Print checkbox user form
        add_action('page_attributes_misc_attributes', [__CLASS__, 'print_checkbox']);

        // Save user form post meta
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Append user form to content
        add_filter('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Send user form with ajax
        add_action('wp_ajax_' . self::$ajax_request, [__CLASS__, 'submit_request']);
        add_action('wp_ajax_nopriv_' . self::$ajax_request, [__CLASS__, 'submit_request']);

        // Update archive caption description
        add_filter('get_the_archive_description', [__CLASS__, 'update_archive_description'], 12);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Add club post type to archives
        add_action('pre_get_posts', [__CLASS__, 'update_archives'], 12);

        // Prepend author meta to content
        add_filter('the_content', [__CLASS__, 'insert_author_link']);

        // Append club link to post content
        add_filter('the_content', [__CLASS__, 'insert_club_link']);

        // Define club settings if still not
        if(!defined('KNIFE_CLUB')) {
            define('KNIFE_CLUB', []);
        }
    }


    /**
     * Register club post type
     */
    public static function register_type() {
        register_post_type(self::$post_type, [
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
        if(is_post_type_archive(self::$post_type)) {
            $club_link = self::get_club_page();

            if(!empty($club_link)) {
                $button_link = sprintf('<div class="caption__button caption__button--club"><a class="button" href="%2$s">%1$s</a></div>',
                    __('Присоединиться', 'knife-theme'),
                    esc_url($club_link)
                );

                $description = $description . $button_link;
            }
        }

        return $description;
    }


    /**
     * Update post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title($title) {
        if(is_post_type_archive(self::$post_type)) {
            $title = sprintf('<h1 class="caption__title caption__title--club">%s</h1>',
                post_type_archive_title('', false)
            );
        }

        return $title;
    }


    /**
     * Insert link to author on single club post
     */
    public static function insert_author_link($content) {
        if(!is_singular(self::$post_type) || !in_the_loop()) {
            return $content;
        }

        $user_id = get_the_author_meta('ID');

        // Allowed description tags
        $allowed = array(
            'a' => [
                'href' => true,
                'target' => true,
                'title' => true
            ]
        );

        $author = [];

        // Add author name with link
        $author[] = sprintf(
            '<div class="author__name"><strong>%s</strong>%s</div>',

            sprintf(
                __('Автор <a href="%1$s">%2$s</a>', 'knife-theme'),
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
            '<div class="author">%s</div>',
            implode("\n", $author)
        );

        return $output . $content;
    }


    /**
     * Insert user club promo link to content
     *
     * @since 1.4
     */
    public static function insert_club_link($content) {
        if(is_singular(self::$post_type) && in_the_loop()) {
            $club_link = self::get_club_page();

            if(!empty($club_link)) {
                $promo_link = sprintf('<figure class="figure figure--club"><a class="button" href="%2$s">%1$s</a>',
                    __('Присоединиться к клубу', 'knife-theme'),
                    esc_url($club_link)
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

            $types[] = self::$post_type;

            $query->set('post_type', $types);
        }
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(get_post_type($post->ID) !== 'page') {
            return;
        }

        $form = get_post_meta($post->ID, self::$meta_form, true);

        printf(
            '<p class="post-attributes-label-wrapper"><span class="post-attributes-label">%s</span></p>',
            __('Дополнительные настройки', 'knife-media')
        );

        printf(
            '<label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label>',
            esc_attr(self::$meta_form),
            __('Добавить форму заявки в клуб', 'knife-theme'),
            checked($form, 1, false)
        );
    }


    /**
     * Save feed post meta
     */
    public static function save_metabox($post_id) {
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_page', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_form])) {
            return delete_post_meta($post_id, self::$meta_form);
        }

        return update_post_meta($post_id, self::$meta_form, 1);
    }


    /**
     * Append user form to page content
     */
    public static function inject_object() {
        if(!is_singular('page')) {
            return;
        }

        $post_id = get_queried_object_id();

        if(!get_post_meta($post_id, self::$meta_form, true)) {
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
            'warning' => __('Не удалось отправить заявку. Попробуйте еще раз', 'knife-theme'),
            'button' => __('Отправить', 'knife-theme'),
            'heading' => __('Отправить заявку', 'knife-theme'),
            'action' => self::$ajax_request,
            'fields' => $fields,
            'classes' => ['form--club'],
            'nonce' => wp_create_nonce(self::$ajax_request)
        ];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_form_request', $options);
    }


    /**
     * Send user form data
     */
    public static function submit_request() {
        if(!check_ajax_referer(self::$ajax_request, 'nonce', false)) {
            wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
        }

        $fields = [];

        foreach(['name', 'email', 'subject', 'text'] as $key) {
            if(empty($_REQUEST[$key])) {
                wp_send_json_error(__('Все поля формы обязательны к заполнению', 'knife-theme'));
            }

            $fields[$key] = stripslashes_deep($_REQUEST[$key]);
        }


        if(method_exists('Knife_Social_Delivery', 'send_telegram')) {
            // Try to find chat in config
            $chat_id = KNIFE_CLUB['chat'] ?? '';

            // Set reuqest id
            $request = absint(get_option(self::$option_request, 313)) + 1;

            $message = [
                'text' => self::get_request($fields, $request),
                'parse_mode' => 'HTML'
            ];

            $response = Knife_Social_Delivery::send_telegram($chat_id, $message);

            if(!is_wp_error($response)) {
                update_option(self::$option_request, $request, false);
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
     * Create request by template
     */
    private static function create_request($fields, $request) {
        extract($fields);

        ob_start();

        $include = get_template_directory() . '/core/include';
        include_once($include . '/templates/club-request.php');

        return ob_get_clean();
    }


    /**
     * Get page link with club form
     *
     * @since 1.12
     */
    private static function get_club_page($link = false) {
        $pages = get_posts([
            'post_type' => 'page',
            'meta_key' => self::$meta_form,
            'meta_value' => 1,
            'posts_per_page' => 1,
            'fields' => 'ids'
        ]);

        if(isset($pages[0])) {
            $link =  get_permalink($pages[0]);
        }

        return $link;
    }
}


/**
 * Load module
 */
Knife_Club_Section::load_module();
