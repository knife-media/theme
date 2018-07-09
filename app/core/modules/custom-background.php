<?php
/**
* Post sticker meta
*
* Custom optional image for posts
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Custom_Background;

class Knife_Custom_Background {
    /**
     * Unique meta to store custom term background
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $meta = '_knife-term-background';


    /**
     * Default taxes term background availible
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private $taxes = ['post_tag', 'category'];


    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'add_assets']);

        // Update Customizer
        add_action('customize_register', [$this, 'update_customizer']);

        // Frontend styles
        add_action('wp_enqueue_scripts', [$this, 'print_background'], 13);

        // update taxes array by filters
        add_action('init', [$this, 'set_taxes'], 20);
    }


    /**
     * Update taxes array by modules filters
     */
    public function set_taxes() {
        $this->taxes = apply_filters('knife_custom_background_taxes', $this->taxes);

        foreach($this->taxes as $tax) {
            add_action("{$tax}_edit_form_fields", [$this, 'print_row'], 10, 2);
            add_action("edited_{$tax}", [$this, 'save_meta']);
        }
    }


    /**
     * Update background controls in admin customizer
     */
    public function update_customizer($wp_customize) {
        // We don't need these options at the moment
        $wp_customize->remove_control('background_preset');
        $wp_customize->remove_control('background_attachment');
        $wp_customize->remove_control('background_repeat');
        $wp_customize->remove_control('background_position');
    }


    /**
     * Print fixed element with custom background
     */
    public function print_background() {
        $backdrop = [];

        $color = $this->get_meta('color', get_background_color());

        if($color !== get_theme_support('custom-background', 'default-color'))
            $backdrop['color'] = $color;

        $image = $this->get_meta('image', get_background_image());

        if($image) {
            $backdrop['image'] = set_url_scheme($image);

            // Set background size style
            $size = $this->get_meta('size', get_theme_mod('background_size'));

            if(in_array($size, ['auto', 'contain', 'cover'], true))
                $backdrop['size'] = $size;
        }

        if(count($backdrop) > 0) {
            wp_localize_script('knife-theme', 'knife_backdrop', $backdrop);
        }
    }


    /**
     * Enqueue assets to term edit screen only
     */
    public function add_assets($hook) {
        $screen = get_current_screen()->taxonomy;

        if($hook !== 'term.php' || !in_array($screen, $this->taxes)) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin scripts
        wp_enqueue_script('knife-custom-background', $include . '/scripts/custom-background.js', ['jquery', 'wp-color-picker'], $version);

        $options = [
            'choose' => __('Выберите фоновое изображение', 'knife-theme')
        ];

        wp_localize_script('knife-custom-background', 'knife_custom_background', $options);
    }


    /**
     * Display custom background options row
     */
    public function print_row($term, $taxonomy) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/custom-background.php');
    }

    /**
     * Save image meta
     */
    public function save_meta($term_id) {
        if(!current_user_can('edit_term', $term_id))
            return;

        if(!empty($_REQUEST[$this->meta]))
            update_term_meta($term_id, $this->meta, $_REQUEST[$this->meta]);
        else
            delete_term_meta($term_id, $this->meta);
    }


    /**
     * Filter background options using term meta
     */
    public function get_meta($option, $default = '') {
        $meta = [];

        /**
         * Filter custom background options
         *
         * @since 1.3
         * @param array $meta
         */
        $meta = apply_filters('knife_custom_background', $meta);

        if(!empty($meta[$option])) {
            return $meta[$option];
        }

        return $default;
    }
}
