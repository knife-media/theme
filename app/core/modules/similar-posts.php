<?php
/**
 * Similar posts
 *
 * Show similar posts grouped by common tags inside single template
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.11
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Similar_Posts {
    /**
     * Cache group to store similar posts
     *
     * @access  private
     * @var     string
     */
    private static $cache_group = 'knife-similar-posts';


    /**
     * Default post type with similar aside
     *
     * @since   1.8
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club'];


    /**
     * Settings page slug
     *
     * @access  private
     * @var     sting
     * @since   1.11
     */
    private static $settings_slug = 'knife-similar';


    /**
     * Store settings page base_id screen
     *
     * @access  private
     * @var     string
     * @since   1.11
     */
    private static $screen_base = null;


    /**
     * Option to store similar promo items
     *
     * @access  private
     * @var     string
     * @since   1.11
     */
    private static $option_promo = 'knife_similar_promo';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Include similar posts data
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Update links returned via tinymce
        add_filter('wp_link_query', [__CLASS__, 'update_link_query'], 12);

        // Add similar promo settings
        add_action('admin_menu', [__CLASS__, 'add_settings_menu'], 9);

        // Process required actions for settings page
        add_action('current_screen', [__CLASS__, 'init_settings_page']);
    }


    /**
     * Add similar promo settings menu
     *
     * @since 1.11
     */
    public static function add_settings_menu() {
        $hookname = add_options_page(
            __('Настройки промо блока рекомендаций', 'knife-theme'),
            __('Блок рекомендаций', 'knife-theme'),
            'unfiltered_html', self::$settings_slug,
            [__CLASS__, 'display_settings_page']
        );

        // Set settings page base_id screen
        self::$screen_base = $hookname;
    }


    /**
     * Process required actions for settings page
     *
     * @since 1.11
     */
    public static function init_settings_page() {
         $current_screen = get_current_screen();

        if($current_screen->base !== self::$screen_base) {
            return;
        }

        if(!current_user_can('unfiltered_html')) {
            wp_die(__('Извините, у вас нет доступа к этой странице', 'knife-theme'));
        }

        // Append similar link if necessary
        self::process_similar_link();

        // Add scripts to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }


    /**
     * Enqueue assets to admin certain screen only
     *
     * @since 1.11
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert admin styles
        wp_enqueue_style('knife-similar-options', $include . '/styles/similar-options.css', [], $version);
    }



    /**
     * Display management page
     *
     * @since 1.11
     */
    public static function display_settings_page() {
        $include = get_template_directory() . '/core/include';

        // Include options template
        include_once($include . '/templates/similar-options.php');
    }


    /**
     * Update links returned via tinymce
     *
     * @since 1.9
     */
    public static function update_link_query($results) {
        foreach($results as &$result) {
            $result['title'] = html_entity_decode($result['title']);
        }

        return $results;
    }


    /**
     * Inject similar posts data
     */
    public static function inject_object() {
        if(is_singular(self::$post_type)) {
            $post_id = get_the_ID();

            if(in_category('news') || has_term('', 'special')) {
                return;
            }

            // Check if promo post
            if(get_post_meta($post_id, '_knife-promo', true)) {
                return;
            }


            $options = [
                'title' => __('Читайте также', 'knife-theme'),
                'action' => __('Similar click', 'knife-theme')
            ];

            // Get similar if not cards
            if(!has_post_format('chat', $post_id)) {
                $similar = self::get_similar($post_id);

                // Append promo
                $similar = self::append_promo($similar);

                if($similar > 0) {
                    $options['similar'] = $similar;
                }
            }

            wp_localize_script('knife-theme', 'knife_similar_posts', $options);
        }
    }


    /**
     * Similar links actions handler
     *
     * @since 1.11
     */
    private static function process_similar_link() {
        $action = isset($_REQUEST['action']) ? sanitize_key($_REQUEST['action']) : '';

        if($action === 'append') {
            self::append_similar_link();
        }

        if($action === 'delete') {
            self::delete_similar_link();
        }
    }


    /**
     * Append similar link to promo
     *
     * @since 1.11
     */
    private static function append_similar_link() {
        check_admin_referer('knife-similar-append');

        // Check if required values not empty
        if(!empty($_POST['title']) && !empty($_POST['link'])) {
            $allowed_html = ['em' => []];

            // Get promo items from settings
            $promo = get_option(self::$option_promo, []);

            // Add value to promo items
            $promo[] = [
                'link' => sanitize_text_field($_POST['link']),
                'title' => wp_kses($_POST['title'], $allowed_html)
            ];

            if(update_option(self::$option_promo, $promo)) {
                return add_settings_error('knife-similar-actions', 'append',
                    __('Ссылка успешно добавлена', 'knife-theme'), 'updated'
                );
            }
        }

        add_settings_error('knife-similar-actions', 'append',
            __('Не удалось добавить ссылку', 'knife-theme')
        );
    }


    /**
     * Delete similar link from promo
     *
     * @since 1.11
     */
    private static function delete_similar_link() {
        check_admin_referer('knife-similar-delete');

        if(!isset($_GET['id'])) {
            return add_settings_error('knife-similar-actions', 'delete',
                __('Не удалось удалить ссылку', 'knife-theme')
            );
        }

        $id = absint($_GET['id']);

        // Get current page admin link
        $admin_url = admin_url('/options-general.php?page=' . self::$settings_slug);

        // Get promo items from settings
        $promo = get_option(self::$option_promo, []);

        unset($promo[$id]);

        // Update promo items
        update_option(self::$option_promo, $promo);

        // Redirect anyway
        wp_redirect(esc_url($admin_url));
        exit;
    }


    /**
     * Get similar posts related on common tags
     *
     * Using get_the_tags function to retrieve terms with primary tag first
     */
    private static function get_similar($post_id) {
        $similar = wp_cache_get($post_id, self::$cache_group);

        if($similar !== false) {
            return $similar;
        }

        $similar = [];

        // Get given post tags
        if($post_terms = get_the_tags($post_id)) {
            $the_terms = wp_list_pluck($post_terms, 'term_id');

            // Get posts with primary tag
            $the_posts = get_posts([
                'posts_per_page' => -1,
                'tag_id' => $the_terms[0],
                'post__not_in' => [$post_id],
                'post_status' => 'publish',
                'ignore_sticky_posts' => true,
                'tax_query' => [
                    [
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => ['news'],
                        'operator' => 'NOT IN'
                    ]
                ]
            ]);

            $related = [];

            foreach(wp_list_pluck($the_posts, 'ID') as $id) {
                $related[$id] = 0;

                foreach(get_the_terms($id, 'post_tag') as $tag) {
                    if(in_array($tag->term_id, $the_terms)) {
                        $related[$id] = $related[$id] + 1;
                    }
                }
            }

            // Sort by tags count
            arsort($related);

            // Get first 9 elements
            $related = array_slice($related, 0, 9, true);

            foreach($related as $id => $count) {
                $similar[] = [
                    'title' => get_the_title($id),
                    'link' => get_permalink($id),
                ];
            }

            // Update similar posts cache by post id
            wp_cache_set($post_id, $similar, self::$cache_group);
        }

        return $similar;
    }


    /**
     * Append similar promo links from query var if exists
     */
    private static function append_promo($similar) {
        $promo = get_option(self::$option_promo, []);

        if(array_filter($promo)) {
            $similar = array_merge($promo, $similar);
        }

        return $similar;
    }
}


/**
 * Load current module environment
 */
Knife_Similar_Posts::load_module();
