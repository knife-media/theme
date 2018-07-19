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

(new Knife_Story_Manager)->init();

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
    private $meta = '_knife-story';


    /**
     * Availible stories options
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $opts = ['background', 'shadow', 'blur'];


    /*
     * Use init function instead of constructor
     */
    public function init() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // Register story post type
        add_action('init', [$this, 'register_story']);

        // Story post metabox
        add_action('add_meta_boxes', [$this, 'add_metabox'], 1);

        // Save story meta
        add_action('save_post', [$this, 'save_meta']);

        // Insert vendor scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 9);

        // Include slider options
        add_action('wp_enqueue_scripts', [$this, 'inject_options'], 12);

        // Remove global backdrop
        add_filter('knife_custom_background', function($meta) {
            if(is_singular($this->slug)) {
                return ['image' => ''];
            }
        });
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

        // Insert scripts for dynaimc wp_editor
        wp_enqueue_editor();

        // Insert admin styles
        wp_enqueue_style('knife-story-manager', $include . '/styles/story-manager.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-story-manager', $include . '/scripts/story-manager.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-story-manager', 'knife_story_manager', $options);
    }


    /**
     * Enqueue glide vendor script
     */
    public function enqueue_assets() {
        if(!is_singular($this->slug))
            return;

        $version = '3.1.0';
        $include = get_template_directory_uri() . '/assets';

        // Enqueue swiper js to bottom
        wp_enqueue_script('glide', $include . '/vendor/glide.min.js', [], $version, true);
    }


    /**
     * Include slider story meta options
     */
    public function inject_options() {
        if(!is_singular($this->slug))
            return;

        $post_id = get_the_ID();

        foreach($this->opts as $item) {
            $options[$item] = get_post_meta($post_id, $this->meta . "-{$item}", true);
        }

        // Add stories options object
        wp_localize_script('knife-theme', 'knife_story_options', $options);
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


        // Update stories meta
        $this->update_stories($this->meta . '-stories', $post_id);

        // Update other story options
        foreach($this->opts as $option) {
            $query = $this->meta . "-{$option}";

            if(!isset($_REQUEST[$query]))
                continue;

            // Get value by query
            $value = $_REQUEST[$query];

            switch($option) {
                case 'background':
                    $value = esc_url($value);

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
     * Update content with story items
     */
    public function get_story($before, $after) {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== $this->slug) {
            return $content;
        }

        $stories = get_post_meta($post_id, $this->meta . '-stories');

        ob_start();

        foreach($stories as $slide) {
            echo $before;

            printf('<div class="glide__slide-wrap">%s</div>',
                $this->append_kicker($post_id) . $this->append_media($slide) . $this->append_entry($slide)
            );

            echo $after;
        }

        return ob_get_clean();
    }


    /**
     * Append kicker based on post title
     */
    private function append_kicker($post_id, $html = '') {
        if($title = get_the_title($post_id)) {
            $html = sprintf('<div class="glide__slide-kicker"><span>%s</span></div>',
                esc_html($title)
            );
        }

        return $html;
    }


    /**
     * Append slide media
     */
    private function append_media($slide, $html = '') {
        if(!empty($slide['media'])) {
            $media = wp_get_attachment_image_src($slide['media'], 'inner');

            if(is_array($media) && count($media) > 2) {

                // Calculate image ratio using width and height
                $ratio = $media[2] / max($media[1], 1);

                $html = sprintf('<div class="glide__slide-image" style="background-image:url(%s); --image-ratio: %s"></div>',
                    $media[0], round($ratio, 3)
                );
            }
        }

        return $html;
    }


    /**
     * Append slide entry
     */
    private function append_entry($slide, $html = '') {
        if(!empty($slide['entry'])) {
            $html = sprintf('<div class="glide__slide-entry">%s</div>',
                apply_filters('the_content', $slide['entry'])
            );
        }

        return $html;
    }


    /**
     * Update stories meta from post-metabox
     */
    private function update_stories($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete stories post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $story) {
            foreach($story as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i])) {
                    $i++;
                }

                $meta[$i][$key] = $value;
            }
        }

        foreach($meta as $key => $item) {
            add_post_meta($post_id, $query, $item);
        }
    }
}
