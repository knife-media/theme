<?php
/**
* Story manager
*
* Custom story post type
*
* @package knife-theme
* @since 1.3
* @version 1.7
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Story_Manager {
    /**
     * Unique slug using for custom post type register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $slug = 'story';


    /**
     * Meta key to store stories in post meta
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $meta = '_knife-story';


    /**
     * Availible stories options
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $opts = ['background', 'shadow', 'blur'];


    /**
     * Metabox save nonce
     *
     * @since   1.5
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-story-nonce';


    /*
     * Use init function instead of constructor
     */
    public static function load_module() {
        // Register story post type
        add_action('after_setup_theme', [__CLASS__, 'register_story']);

        // Include story single template
        add_filter('single_template', [__CLASS__, 'include_single']);

        // Include story archive template
        add_filter('archive_template', [__CLASS__, 'include_archive']);

        // Insert admin side assets
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Story post metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox'], 1);

        // Save story meta
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add user post type to author archive
        add_action('pre_get_posts', [__CLASS__, 'update_author_archive'], 12);

        // Change posts count on story archive
        add_action('pre_get_posts', [__CLASS__, 'update_count']);

        // Insert vendor scripts and styles
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_dependences'], 9);

        // Include slider options
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_stories'], 12);
    }


    /**
     * Include single story template
     *
     * @since 1.4
     */
    public static function include_archive($template) {
        if(is_post_type_archive(self::$slug)) {
            $new_template = locate_template(['templates/archive-story.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Include archive story template
     *
     * @since 1.4
     */
    public static function include_single($template) {
        if(is_singular(self::$slug)) {
            $new_template = locate_template(['templates/single-story.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function enqueue_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $post_id = get_the_ID();

        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert scripts for dynaimc wp_editor
        wp_enqueue_editor();

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert admin styles
        wp_enqueue_style('knife-story-metabox', $include . '/styles/story-metabox.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-story-metabox', $include . '/scripts/story-metabox.js', ['jquery', 'jquery-ui-sortable'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-story-metabox', 'knife_story_metabox', $options);
    }


    /**
     * Enqueue glide vendor script
     */
    public static function inject_dependences() {
        if(is_singular(self::$slug)) {
            $version = '3.2.4';
            $include = get_template_directory_uri() . '/assets';

            // Enqueue swiper js to bottom
            wp_enqueue_script('glide', $include . '/vendor/glide.min.js', [], $version, true);
        }
    }


    /**
     * Include slider story meta options
     */
    public static function inject_stories() {
        if(is_singular(self::$slug)) {
            $post_id = get_the_ID();
            $stories = self::convert_stories($post_id);

            $options = [];

            foreach(self::$opts as $item) {
                $options[$item] = get_post_meta($post_id, self::$meta . "-{$item}", true);
            }

            $options['action'] = __('Share story — last', 'knife-media');

            // Add stories options object
            wp_localize_script('knife-theme', 'knife_story_options', $options);

            // Add stories items
            wp_localize_script('knife-theme', 'knife_story_stories', $stories);
        }
    }


    /**
     * Register story post type
     */
    public static function register_story() {
        register_post_type(self::$slug, [
            'labels'                    => [
                'name'                  => __('Истории', 'knife-theme'),
                'singular_name'         => __('История', 'knife-theme'),
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
            'description'           => __('Лучшие фото-истории интернета', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author'],
            'hierarchical'          => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 9,
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
     * Append to author archive loop story posts
     */
    public static function update_author_archive($query) {
        if(!is_admin() && is_author() && $query->is_main_query()) {
            $types = $query->get('post_type');

            if(!is_array($types)) {
                $types = ['post'];
            }

            $types[] = self::$slug;

            $query->set('post_type', $types);
        }
    }


    /**
     * Change posts_per_page for stories archive template
     *
     * @since 1.4
     */
    public static function update_count($query) {
        if($query->is_main_query() && $query->is_post_type_archive(self::$slug)) {
            $query->set('posts_per_page', 12);
        }
    }


    /**
     * Add story manage metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-story-metabox', __('Настройки истории', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
    }


    /**
     * Display story slides metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/story-metabox.php');
    }


    /**
     * Save post options
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        if(!isset($_REQUEST[self::$nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }


        // Update stories meta
        self::update_stories(self::$meta . '-stories', $post_id);

        // Update other story options
        foreach(self::$opts as $option) {
            $query = self::$meta . "-{$option}";

            if(isset($_REQUEST[$query])) {
                // Get value by query
                $value = $_REQUEST[$query];

                if(in_array($option, ['shadow', 'blur'])) {
                    $value = absint($value);
                }

                if($option === 'background') {
                    $value = esc_url($value);
                }

                update_post_meta($post_id, $query, $value);
            }
        }
    }


    /**
     * Update stories meta from post-metabox
     */
    private static function update_stories($query, $post_id, $meta = [], $i = 0) {
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

                if(strlen($value) > 0) {
                    if($key === 'entry') {
                        $meta[$i][$key] = wp_kses_post($value);
                    }

                    if($key === 'media') {
                        $meta[$i][$key] = absint($value);
                    }
                }
            }
        }

        foreach($meta as $key => $item) {
            add_post_meta($post_id, $query, $item);
        }
    }


    /**
     * Convert stories post meta to object
     */
    private static function convert_stories($post_id, $stories = []) {
        $items = get_post_meta($post_id, self::$meta . '-stories');

        foreach($items as $i => $slide) {
            if(!empty($slide['media'])) {
                $media = wp_get_attachment_image_src($slide['media'], 'inner');

                if(is_array($media) && count($media) > 2) {
                    // Calculate image ratio using width and height
                    $ratio = $media[2] / max($media[1], 1);

                    $stories[$i]['image'] = $media[0];
                    $stories[$i]['ratio'] = $ratio;
                }
            }

            if(!empty($slide['entry'])) {
                $stories[$i]['entry'] = apply_filters('the_content', $slide['entry']);
            }

            if($title = get_the_title($post_id)) {
                $stories[$i]['kicker'] = esc_html($title);
            }
        }

        return $stories;
    }
}


/**
 * Load current module environment
 */
Knife_Story_Manager::load_module();
