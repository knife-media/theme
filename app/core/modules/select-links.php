<?php
/**
* Selection type
*
* Custom post type for manual articles select
*
* @package knife-theme
* @since 1.4
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
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Apply theme hooks
        add_action('after_setup_theme', [__CLASS__, 'setup_actions']);


        // Register select post type
        add_action('init', [__CLASS__, 'register_type']);

        // Add select metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Save metabox
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Filter content to show custom links
        add_filter('the_content', [__CLASS__, 'update_content']);
    }


    /**
     * Setup theme hooks
     */
    public static function setup_actions() {
        // Add post lead to post type editor
        add_filter('knife_post_lead_type', function($default) {
            $default[] = self::$slug;

            return $default;
        });
    }


    /**
     * Register select post type
     */
    public static function register_type() {
        register_post_type(self::$slug, [
            'labels'                => [
                'name'              => __('Подборка', 'knife-theme'),
                'singular_name'     => __('Запись в подборку', 'knife-theme'),
                'add_new'           => __('Добавить запись', 'knife-theme'),
                'menu_name'         => __('Подборки', 'knife-theme')
            ],
            'label'                 => __('Подборка', 'knife-theme'),
            'description'           => __('Подборки статей', 'knife-theme'),
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
     * Update content with custom links
     */
    public static function update_content($content) {
        $post_id = get_the_ID();

        if(get_post_type($post_id) !== self::$slug) {
            return $content;
        }

        $html = false;//get_transient("knife_{self::$slug}_{$post_id}");

        if($html === false) {
            $items = get_post_meta($post_id, self::$meta . '-items');

            ob_start();

            foreach($items as $item) {
                self::process_item($item);
            }

            $html = ob_get_clean();
            set_transient("knife_{self::$slug}_{$post_id}", $html, 24 * HOUR_IN_SECONDS);
        }

        return sprintf('<div class="post__content-selects">%s</div>', $html);
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

        // insert admin styles
        wp_enqueue_style('knife-select-links', $include . '/styles/select-links.css', [], $version);

        // insert admin scripts
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
        if(empty($_REQUEST[$query]))
            return;

        // delete select post meta to create it again below
        delete_post_meta($post_id, $query);

        foreach($_REQUEST[$query] as $item) {
            foreach($item as $key => $value) {
                if(isset($meta[$i]) && array_key_exists($key, $meta[$i])) {
                    $i++;
                }

                switch($key) {
                    case 'text':
                        $value = sanitize_text_field($value);
                        break;

                    case 'link':
                        $value = esc_url($value);
                        break;
                }

                $meta[$i][$key] = $value;
            }
        }

        foreach($meta as $item) {
            if(!empty($item['text']) && !empty($item['link'])) {
                add_post_meta($post_id, $query, $item);
            }
        }
    }


    private static function process_item($item) {
        if(empty($item['text']) || empty($item['link'])) {
            return;
        }

        $post_id = url_to_postid($item['link']);

        echo '<div class="select">';

        if($post_id > 0) {
            global $post;

            $post = get_post($post_id);
            setup_postdata($post);

            the_info(
                '<div class="select__meta meta">', '</div>',
                ['author', 'date']
            );

            wp_reset_postdata();
        }

        printf('<a class="select__link" href="%2$s">%1$s</a>',
            esc_html($item['text']),
            esc_url($item['link'])
        );

        echo '</div>';
    }
}


/**
 * Load current module environment
 */
Knife_Select_Links::load_module();
