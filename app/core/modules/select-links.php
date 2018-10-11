<?php
/**
* Selection type
*
* Custom post type for manual articles select
*
* @package knife-theme
* @since 1.4
* @version 1.5
*/

if (!defined('WPINC')) {
    die;
}

class Knife_Select_Links {
    /**
     * Unique slug using for custom post type register and url
     *
     * @access  private
     * @var     string
     */
    private static $slug = 'select';


    /**
     * Unique meta using for saving post data
     *
     * @access  private
     * @var     string
     */
    private static $meta = '_knife-select';


    /**
     * Unique nonce string using for ajax referer check
     *
     * @access  private static
     * @var     string
     */
    private static $nonce = 'knife-select-nonce';


   /**
    * Ajax action
    *
    * @access  private static
    * @var     string
    */
    private static $action = 'knife-select-title';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Register select post type
        add_action('init', [__CLASS__, 'register_type']);

        // Change single post type template path
        add_action('single_template', [__CLASS__, 'include_single']);

        // Add select metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Get postid by url
        add_action('wp_ajax_' . self::$action, [__CLASS__, 'get_title']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Update title with strong number
        add_filter('the_title', [__CLASS__, 'select_title'], 10, 2);

        // Filter content to show custom links
        add_filter('the_content', [__CLASS__, 'update_content']);
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                => [
                'name'              => __('Подборки', 'knife-theme'),
                'singular_name'     => __('Запись в подборку', 'knife-theme'),
                'add_new'           => __('Добавить запись', 'knife-theme'),
                'menu_name'         => __('Подборки', 'knife-theme')
            ],
            'label'                 => __('Подборка', 'knife-theme'),
            'supports'              => ['title', 'thumbnail', 'revisions', 'excerpt', 'author'],
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 6,
            'menu_icon'             => 'dashicons-images-alt',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true
        ]);
    }


    /**
     * Include single select template
     */
    public static function include_single($template) {
        if(is_singular(self::$slug)) {
            $new_template = locate_template(['templates/single-select.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Filter the select title
     *
     * @since 1.5
     */
    public static function select_title($title, $post_id) {
        if(!is_admin()) {
            $words = explode(' ', ltrim($title));

            if(is_numeric($words[0])) {
                $title = "<strong>{$words[0]}</strong> " . implode(' ', array_slice($words, 1));
            }
        }

        return $title;
    }


    /**
     * Get postid by url using admin side ajax
     */
    public static function get_title() {
        check_admin_referer(self::$nonce, 'nonce');

        if(isset($_POST['link'])) {
            $link = sanitize_text_field($_POST['link']);

            // Get post id by url
            $post_id = url_to_postid($link);

            if($post_id > 0) {
                $title = get_the_title($post_id);

                wp_send_json_success($title);
            }
        }

        wp_send_json_error();
    }


    /**
     * Update content with custom links
     */
    public static function update_content($content) {
        if(!is_singular(self::$slug) || !in_the_loop()) {
            return $content;
        }

        $items = get_post_meta(get_the_ID(), self::$meta . '-items');

        foreach($items as $item) {
            $content = self::get_item($item, $content);
        }

        return $content;
    }


    /**
     * Add select metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-select-metabox', __('Подборка статей'), [__CLASS__, 'display_metabox'], self::$slug, 'normal', 'high');
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

        // Insert admin styles
        wp_enqueue_style('knife-select-links', $include . '/styles/select-links.css', [], $version);

        // Insert admin scripts
        wp_enqueue_script('knife-select-links', $include . '/scripts/select-links.js', ['jquery', 'jquery-ui-sortable'], $version);
    }


    /**
     * Display select link metabox
     */
    public static function display_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/select-metabox.php');
    }


    /**
     * Save post options
     *
     * TODO: verify nonce
     */
    public static function save_metabox($post_id) {
        if(get_post_type($post_id) !== self::$slug) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update items meta
        self::update_items(self::$meta . '-items', $post_id);
    }


    /**
     * Update select items meta from post-metabox
     */
    private static function update_items($query, $post_id, $meta = [], $i = 0) {
        if(empty($_REQUEST[$query])) {
            return;
        }

        // Delete select post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            foreach($item as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i])) {
                    $i++;
                }

                if(!empty($value)) {
                    $meta[$i][$key] = sanitize_text_field($value);

                    if($key === 'link') {
                        $meta[$i]['post'] = url_to_postid($value);
                    }
                }
            }
        }

        foreach($meta as $item) {
            add_post_meta($post_id, $query, $item);
        }
    }


    /**
     * Get select item from meta
     */
    private static function get_item($item, $content = '', $meta = '') {
        global $post;

        if(intval($item['post']) > 0) {
            $post = get_post($item['post']);
            setup_postdata($post);

            $meta = the_info(
                '<div class="select__info">', '</div>',
                ['author', 'date'], false
            );

            wp_reset_postdata();
        }

        $link = sprintf('<a class="select__link" href="%2$s">%1$s</a>',
            esc_html($item['text'] ?? ''), esc_url($item['link'] ?? '')
        );

        $select = sprintf('<div class="select">%s</div>',
            $meta . $link
        );

        return $content . $select;
    }
}


/**
 * Load current module environment
 */
Knife_Select_Links::load_module();
