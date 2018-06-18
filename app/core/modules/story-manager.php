<?php
/**
* Story manager
*
* Custom story post type
*
* @package knife-theme
* @since 1.3
*/

if (!defined('WPINC')) {
    die;
}

new Knife_Story_Manager;

class Knife_Story_Manager {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $slug = 'story';


    /**
     * Meta key to store stories in post meta
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $meta = 'knife-story';


    /**
     * Availible stories options
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $opts = ['background', 'shadow', 'blur', 'effect'];


    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // register story post type
        $this->register_story();

        // core post metabox
        add_action('add_meta_boxes', [$this, 'add_metabox'], 1);

        // save story meta
        add_action('save_post', [$this, 'save_meta']);

        // insert vendor scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 9);

        // include swiper options
        add_action('wp_enqueue_scripts', [$this, 'inject_object'], 12);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public function add_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php']))
            return;

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== $this->slug)
            return;

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert scripts for dynaimc wp_editor
        wp_enqueue_editor();

        // insert admin styles
        wp_enqueue_style('knife-story-manager', $include . '/styles/story-manager.css', [], $version);

        // insert admin scripts
        wp_enqueue_script('knife-story-manager', $include . '/scripts/story-manager.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-story-manager', 'knife_story_manager', $options);
    }


    /**
     * Enqueue swiper scripts and styles
     */
    public function enqueue_assets() {

        if(!is_singular($this->slug))
            return;

        $version = '4.2.2';
        $include = get_template_directory_uri() . '/assets';

        // enqueue swiper css
        wp_enqueue_style('swiper', $include . '/styles/swiper.min.css', [], $version);

        // enqueue swiper js to bottom
        wp_enqueue_script('swiper', $include . '/scripts/swiper.min.js', [], $version, true);
    }


    /**
     * Include swiper story meta options
     */
    public function inject_object() {
        if(!is_singular($this->slug))
            return;

        $post_id = get_the_ID();
        $stories = $this->_convert_stories($post_id);

        foreach($this->opts as $item) {
            $options[$item] = get_post_meta($post_id, $this->meta . "-{$item}", true);
        }

        // add stories options object
        wp_localize_script('knife-theme', 'knife_story_options', $options);

        // add stories items
        wp_localize_script('knife-theme', 'knife_story_stories', $stories);
    }


    /**
     * Register story post type
     */
    public function register_story() {
        register_post_type($this->slug, [
            'labels'                    => [
                'menu_name'             => __('Истории', 'knife-theme'),
                'name_admin_bar'        => __('Историю', 'knife-theme'),
                'parent_item_colon'     => __('Родительская история:', 'knife-theme'),
                'all_items'             => __('Все истории', 'knife-theme'),
                'add_new_item'          => __('Добавить новую историю', 'knife-theme'),
                'add_new'               => __('Добавить новую', 'knife-theme'),
                'new_item'              => __('Новая история', 'knife-theme'),
                'edit_item'             => __('Редактировать историю', 'knife-theme'),
                'update_item'           => __('Обновить историю', 'knife-theme'),
                'view_item'             => __('Просмотреть историю', 'knife-theme'),
                'view_items'            => __('Просмотреть истории', 'knife-theme'),
                'search_items'          => __('Искать историю', 'knife-theme'),
                'insert_into_item'      => __('Добавить в историю', 'knife-theme'),
                'not_found'             => __('Историй не найдено', 'knife-theme'),
                'not_found_in_trash'    => __('В корзине ничего не найдено', 'knife-theme')
            ],
            'label'                 => __('Истории', 'knife-theme'),
            'description'           => __('Слайды с интерактивными историями', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author'],
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-slides',
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
     * Add story manage metabox
     */
    public function add_metabox() {
        add_meta_box('knife-story-metabox', __('Настройки истории', 'knife-theme'), [$this, 'display_metabox'], $this->slug, 'normal', 'high');
    }


    /**
     * Display story slides metabox
     */
    public function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/story-metabox.php');
    }


    /**
     * Save post options
     */
    public function save_meta($post_id) {
        if(get_post_type($post_id) !== $this->slug)
            return;

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if(!current_user_can('edit_post', $post_id))
            return;


        // update stories meta
        $this->_update_stories($this->meta . '-stories', $post_id);

        // update other story options
        foreach($this->opts as $option) {
            $query = $this->meta . "-{$option}";

            if(!isset($_REQUEST[$query]))
                continue;

            // get value by query
            $value = $_REQUEST[$query];

            switch($option) {
                case 'background':
                    $value = esc_url($value);

                    break;

                case 'effect':
                    $value = esc_html($value);

                    break;

                case 'shadow':
                    $value = absint($value);

                    break;

                case 'blur':
                    $value = absint($value);

                    break;
            }

            update_post_meta($post_id, $query, $value);
        }
    }


    /**
     * Convert stories post meta to object
     */
    private function _convert_stories($post_id, $stories = []) {
        $items = get_post_meta($post_id, $this->meta . '-stories');

        foreach($items as $i => $story) {
            if(!empty($story['media'])) {
                $stories[$i]['image'] = wp_get_attachment_image_url($story['media'], 'outer');

                if($caption = wp_get_attachment_caption($story['media']))
                    $stories[$i]['caption'] = $caption;
            }

            if(!empty($story['entry'])) {
                $stories[$i]['entry'] = apply_filters('the_content', $story['entry']);
            }
        }

        return $stories;
    }


    /**
     * Update stories meta from post-metabox
     */
    private function _update_stories($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query]))
            return;

        // delete stories post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $story) {
            foreach($story as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i]))
                    $i++;

                switch($key) {
                    case 'media':
                        $value = absint($value);
                        break;
                }

                $meta[$i][$key] = $value;
            }
        }

        foreach($meta as $key => $item) {
            add_post_meta($post_id, $query, $item);
        }
    }
}