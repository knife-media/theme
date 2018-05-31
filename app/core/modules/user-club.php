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
     * User form fields
     *
     * @since   1.3
     * @access  private
     * @var     array
     */
    private $fields = [];


    public function __construct() {

        // set form fields
        $this->fields = [
            'name' => [
                'element' => 'input',
                'type' => 'text',
                'placeholder' => __('Ваше имя', 'knife-theme')
            ],
            'email' => [
                'element' => 'input',
                'type' => 'email',
                'placeholder' => __('Электронная почта', 'knife-theme')
            ],
            'subject' => [
                'element' => 'input',
                'type' => 'text',
                'placeholder' => __('О чем хотите писать', 'knife-theme')
            ],
            'text' => [
                'element' => 'textarea',
                'placeholder' => __('Текст поста целиком', 'knife-theme')
            ],
            'submit' => [
                'element' => 'button',
                'type' => 'submit',
                'value' => __('Отправить', 'knife-theme')
            ]
        ];


        // register club post type
        add_action('init', [$this, 'register_club']);

        // print checkbox user form
        add_action('page_attributes_misc_attributes', [$this, 'print_checkbox']);

        // save user form post meta
        add_action('save_post', [$this, 'save_meta']);

        // append user form to content
        add_filter('wp_enqueue_scripts', [$this, 'inject_object'], 12);

        // receive user form with ajax
        add_action('wp_ajax_knife_user_form', [$this, 'submit_form']);
        add_action('wp_ajax_nopriv_knife_user_form', [$this, 'submit_form']);
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
            'capability_type'       => 'post',
        ]);
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

		$options = [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('knife_user_form'),
			'action' => 'knife_user_form',
			'fields' => $this->fields
		];

        // add user form fields
        wp_localize_script('knife-theme', 'knife_user_form', $options);
    }


    /**
     * Save user form data
     */
    public function submit_form() {
        print_r($_POST);
        die;
    }
}
