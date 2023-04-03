<?php
/**
 * Custom background
 *
 * Backdrop for sites and custom archives
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.16
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Custom_Background {
    /**
     * Unique meta to store custom term background
     *
     * @access  public
     * @var     string
     * @since   1.3
     */
    public static $term_meta = '_knife-term-background';


    /**
     * Default taxes term background availible
     *
     * @access  public
     * @var     array
     * @since   1.3
     */
    public static $taxonomies = ['special'];


    /**
     * Default post type with custom background metabox
     *
     * @access  public
     * @var     array
     * @since   1.7
     */
    public static $post_type = ['post', 'club', 'quiz', 'page'];


    /**
     * Unique meta to store post background options
     *
     * @access  public
     * @var     string
     * @since   1.7
     */
    public static $meta_background = '_knife-background';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Add background form fields
        add_action('admin_init', [__CLASS__, 'add_options_fields']);

        // Save background post meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Add custom background metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Update Customizer
        add_action('customize_register', [__CLASS__, 'update_customizer']);

        // Frontend styles
        add_action('wp_enqueue_scripts', [__CLASS__, 'print_background'], 13);
    }


    /**
     * Add custom background metabox only for admins
     *
     * @since 1.7
     */
    public static function add_metabox() {
        if(current_user_can('unfiltered_html')) {
            add_meta_box('knife-background-metabox',
                __('Произвольный фон', 'knife-theme'),
                [__CLASS__, 'display_metabox'], self::$post_type, 'side'
            );

            // Enqueue post metabox scripts
            add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_metabox_assets']);
        }
    }


    /**
     * Display background metabox
     *
     * @since 1.7
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/background-metabox.php');
    }


    /**
     * Add adminside edit form fields
     *
     * @since 1.4
     */
    public static function add_options_fields() {
        foreach(self::$taxonomies as $tax) {
            add_action("{$tax}_edit_form_fields", [__CLASS__, 'print_options_row'], 10, 2);
            add_action("edited_{$tax}", [__CLASS__, 'save_options']);
        }

        // Enqueue term options scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_options_assets']);
    }


    /**
     * Update background controls in admin customizer
     */
    public static function update_customizer($wp_customize) {
        // We don't need these options at the moment
        $wp_customize->remove_control('background_preset');
        $wp_customize->remove_control('background_attachment');
        $wp_customize->remove_control('background_repeat');
        $wp_customize->remove_control('background_position');
    }


    /**
     * Print fixed element with custom background
     */
    public static function print_background() {
        $backdrop = [];

        $defaults = [
            'color' => get_background_color(),
            'image' => get_background_image(),
            'size' => get_theme_mod('background_size')
        ];

        // Get term meta only once
        $meta = (array) self::get_meta();

        if(!array_filter($meta)) {
            $meta = $defaults;
        }

        if(!empty($meta['color'])) {
            $meta['color'] = ltrim($meta['color'], '#');

            if($meta['color'] !== get_theme_support('custom-background', 'default-color')) {
                $backdrop['color'] = $meta['color'];
            }
        }

        if(!empty($meta['image'])) {
            $backdrop['image'] = set_url_scheme($meta['image']);

            if(in_array($meta['size'], ['auto', 'contain', 'cover'], true)) {
                $backdrop['size'] = $meta['size'];
            }
        }

        if(count($backdrop) > 0) {
            wp_localize_script('knife-theme', 'knife_backdrop', $backdrop);
        }
    }


    /**
     * Enqueue assets to term edit screen only
     *
     * @since 1.7
     */
    public static function enqueue_options_assets($hook) {
        // Current screen object
        $screen = get_current_screen();

        if($hook !== 'term.php' || !in_array($screen->taxonomy,  self::$taxonomies)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin scripts
        wp_enqueue_script('knife-background-options', $include . '/scripts/background-options.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-background-options', 'knife_background_options', $options);
    }


    /**
     * Enqueue assets for metabox
     *
     * @since 1.7
     */
    public static function enqueue_metabox_assets($hook) {
        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        // Current screen object
        $screen = get_current_screen();

        if(!in_array($screen->post_type,  self::$post_type)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin scripts
        wp_enqueue_script('knife-background-metabox', $include . '/scripts/background-metabox.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-background-metabox', 'knife_background_metabox', $options);
    }


    /**
     * Display custom background options row
     */
    public static function print_options_row($term, $taxonomy) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/background-options.php');
    }


    /**
     * Save background post meta
     *
     * @since 1.7
     */
    public static function save_meta($post_id) {
        if(isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('unfiltered_html', $post_id)) {
            return;
        }

        if (!isset($_REQUEST[self::$meta_background])) {
            return;
        }

        $background = array_filter($_REQUEST[self::$meta_background]);

        if(empty($background)) {
            return delete_post_meta($post_id, self::$meta_background);
        }

        update_post_meta($post_id, self::$meta_background, $background);
    }


    /**
     * Save term options
     */
    public static function save_options($term_id) {
        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        if(empty($_REQUEST[self::$term_meta])) {
            return delete_term_meta($term_id, self::$term_meta);
        }

        // Filter empty values
        $background = array_filter($_REQUEST[self::$term_meta]);

        return update_term_meta($term_id, self::$term_meta, $background);
    }


    /**
     * Filter background options using term meta
     *
     * @link https://github.com/knife-media/knife-theme/issues/49
     */
    public static function get_meta($background = []) {
        $object_id = get_queried_object_id();

        if(is_singular(self::$post_type)) {
            $background = (array) get_post_meta($object_id, self::$meta_background, true);

            if(array_filter($background)) {
                return $background;
            }

            foreach(self::$taxonomies as $tax) {
                if(!has_term('', $tax)) {
                    continue;
                }

                // Loop over all tax terms
                foreach(get_the_terms($object_id, $tax) as $term) {
                    if($background = get_term_meta($term->term_id, self::$term_meta, true)) {
                        break 2;
                    }
                }
            }
        }

        /**
         * We have to check archives separately
         *
         * @link https://core.trac.wordpress.org/ticket/18636
         */
        if(is_tax() || is_tag() || is_category()) {
            return (array) get_term_meta($object_id, self::$term_meta, true);
        }

        return $background;
    }
}


/**
 * Load current module environment
 */
Knife_Custom_Background::load_module();
