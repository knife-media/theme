<?php
/**
* Custom background
*
* Backdrop for sites and custom archives
*
* @package knife-theme
* @since 1.2
* @version 1.4
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Custom_Background {
    /**
     * Unique meta to store custom term background
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $meta = '_knife-term-background';


    /**
     * Default taxes term background availible
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $taxes = ['special'];


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Add background form fields
        add_action('admin_init', [__CLASS__, 'add_options_fields']);

        // Update Customizer
        add_action('customize_register', [__CLASS__, 'update_customizer']);

        // Frontend styles
        add_action('wp_enqueue_scripts', [__CLASS__, 'print_background'], 13);
    }


    /**
     * Add adminside edit form fields
     *
     * @since 1.4
     */
    public static function add_options_fields() {
        foreach(self::$taxes as $tax) {
            add_action("{$tax}_edit_form_fields", [__CLASS__, 'print_options_row'], 10, 2);
            add_action("edited_{$tax}", [__CLASS__, 'save_options_meta']);
        }

        // Enqueue scripts only on admin screen
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_options_assets']);
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
        $meta = wp_parse_args(self::get_meta(), $defaults);

        $meta['color'] = ltrim($meta['color'], '#');

        if($meta['color'] !== get_theme_support('custom-background', 'default-color')) {
            $backdrop['color'] = $meta['color'];
        }

        if($meta['image']) {
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
     */
    public static function add_options_assets($hook) {
        $screen = get_current_screen()->taxonomy;

        if($hook !== 'term.php' || !in_array($screen, self::$taxes)) {
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
     * Display custom background options row
     */
    public static function print_options_row($term, $taxonomy) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/background-options.php');
    }

    /**
     * Save image meta
     */
    public static function save_options_meta($term_id) {
        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta])) {
            return delete_term_meta($term_id, self::$meta);
        }

        $meta = [];

        foreach($_REQUEST[self::$meta] as $key => $value) {
            if((string) $value !== '') {
                $meta[$key] = $value;
            }
        }

        update_term_meta($term_id, self::$meta, $meta);
    }


    /**
     * Filter background options using term meta
     *
     * @link https://github.com/knife-media/knife-theme/issues/49
     */
    public static function get_meta() {
        $background = [];

        /*
         * We have to check archives separately
         *
         * @link https://core.trac.wordpress.org/ticket/18636
         */
        if(is_tax() || is_tag() || is_category()) {
            $background = get_term_meta(get_queried_object_id(), self::$meta, true);
        }

        foreach(self::$taxes as $tax) {
            if(!is_single() || !has_term('', $tax)) {
                continue;
            }

            // Loop over all tax terms
            foreach(get_the_terms(get_queried_object_id(), $tax) as $term) {
                if($background = get_term_meta($term->term_id, self::$meta, true)) {
                    break 2;
                }
            }
        }

        return $background;
    }
}


/**
 * Load current module environment
 */
Knife_Custom_Background::load_module();
