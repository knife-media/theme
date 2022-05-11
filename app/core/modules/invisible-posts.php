<?php
/**
 * Invisible posts
 *
 * Add invisible post status to hide posts from all archives and feeds
 *
 * @package knife-theme
 * @since 1.15
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Invisible_Posts {
    /**
     * Post status for invisible posts
     *
     * @access  public
     * @var     string
     */
    public static $post_status = 'invisible';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include scripts admin page only
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_assets']);

        // Load custom post functions
        add_action('init', [__CLASS__, 'add_post_status']);

        // Display invisible state in posts list
        add_filter('display_post_states', [__CLASS__, 'display_invisible_state'], 10, 2);
    }


    /**
     * Load custom post functions only for current post name
     */
    public static function add_post_status() {
        register_post_status(self::$post_status, [
            'label'                     => __('Невидимый', 'knife-theme'),
            'public'                    => true,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop(
                'Скрытые <span class="count">(%s)</span>',
                'Скрытые <span class="count">(%s)</span>',
                'knife-theme'
            ),
        ]);
    }


    /**
     * Display teaser state in posts lists
     */
    public static function display_invisible_state($states, $post) {
        if(get_post_status($post->ID) === self::$post_status) {
            $states[] = __('Скрытый', 'knife-theme');
        }

        return $states;
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_assets($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin script
        wp_enqueue_script('knife-invisible-status', $include . '/scripts/invisible-status.js', ['jquery'], $version);

        $options = [
            'current' => $post->post_status,
            'label'   => __('Скрытый', 'knife-theme'),
            'target'  => self::$post_status,
        ];

        // Add current post status
        wp_localize_script('knife-invisible-status', 'knife_invisible_status', $options);
    }
}


/**
 * Load current module environment
 */
Knife_Invisible_Posts::load_module();
