<?php
/**
 * Special projects
 *
 * Custom special projects taxonomy settings
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.9
 */


if (!defined('WPINC')) {
    die;
}


class Knife_Special_Projects {
    /**
     * Unique slug using for taxonomy register and url
     *
     * @since   1.3
     * @access  private
     * @var     string
     */
    private static $taxonomy = 'special';


    /**
     * Unique meta to store custom term color
     *
     * @since   1.4
     * @access  private
     * @var     string
     */
    private static $term_meta = '_knife-special-options';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.4
     */
    public static function load_module() {
        // Register taxonomy
        add_action('init', [__CLASS__, 'register_taxonomy']);

        // Add special options form fields
        add_action('admin_init', [__CLASS__, 'add_options_fields']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 15);
    }


    /**
     * Create custom taxonomy
     */
    public static function register_taxonomy() {
        register_taxonomy(self::$taxonomy, 'post', [
            'labels' => [
                'name'                       => __('Спецпроекты', 'knife-theme'),
                'singular_name'              => __('Спецпроект', 'knife-theme'),
                'search_items'               => __('Поиск', 'knife-theme'),
                'popular_items'              => __('Популярные спецпроекты', 'knife-theme'),
                'all_items'                  => __('Все', 'knife-theme'),
                'edit_item'                  => __('Редактировать', 'knife-theme'),
                'update_item'                => __('Обновить', 'knife-theme'),
                'add_new_item'               => __('Добавить новый', 'knife-theme'),
                'new_item_name'              => __('Новый спецпроект', 'knife-theme'),
                'separate_items_with_commas' => __('Разделить записи запятыми', 'knife-theme'),
                'add_or_remove_items'        => __('Добавить или удалить тип', 'knife-theme'),
                'choose_from_most_used'      => __('Наиболее используемые', 'knife-theme'),
                'not_found'                  => __('Не найдено', 'knife-theme'),
                'menu_name'                  => __('Спецпроекты', 'knife-theme'),
            ],
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => ['slug' => self::$taxonomy],
            'meta_box_cb'           => [__CLASS__, 'print_metabox']
        ]);
    }


    /**
     * Add adminside edit form fields
     *
     * @since 1.4
     */
    public static function add_options_fields() {
        // Print special options row
        add_action(self::$taxonomy . "_edit_form_fields", [__CLASS__, 'print_options_row'], 9);

        // Save admin side options meta
        add_action("edited_" . self::$taxonomy, [__CLASS__, 'save_options_meta']);

        // Enqueue scripts only on admin screen
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_options_scripts']);
    }


    /**
     * Display custom special options row
     *
     * @since 1.4
     */
    public static function print_options_row($term) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/special-options.php');
    }


    /**
     * Enqueue assets to term edit screen only
     *
     * @since 1.4
     */
    public static function add_options_scripts($hook) {
        $screen = get_current_screen()->taxonomy;

        if($hook !== 'term.php' || $screen !== self::$taxonomy) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // Insert wp media scripts
        wp_enqueue_media();

        // Insert color picker scripts
        wp_enqueue_style('wp-color-picker');

        // Insert admin scripts
        wp_enqueue_script('knife-special-options', $include . '/scripts/special-options.js', ['jquery', 'wp-color-picker'], $version);
    }


    /**
     * Add custom taxonomy metabox without tabs and add-new feature
     *
     * @since 1.5
     */
    public static function print_metabox($post, $box) {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/special-metabox.php');
    }


    /**
     * Save color meta
     *
     * @since 1.4
     */
    public static function save_options_meta($term_id) {
        if(!current_user_can('edit_term', $term_id)) {
            return;
        }

        if(empty($_REQUEST[self::$term_meta])) {
            return delete_term_meta($term_id, self::$term_meta);
        }

        $meta = [];

        foreach($_REQUEST[self::$term_meta] as $key => $value) {
            if(!empty($value)) {
                $meta[$key] = sanitize_hex_color($value);
            }
        }

        update_term_meta($term_id, self::$term_meta, $meta);
    }


    /**
     * Update special post archive caption title
     *
     * @since 1.4
     */
    public static function update_archive_title($title) {
        if(!is_tax(self::$taxonomy)) {
            return $title;
        }

        $options = get_term_meta(get_queried_object_id(), self::$term_meta, true);

        if(empty($options['color'])) {
            $title =  sprintf(
                '<h1 class="tagline-title tagline-title--%2$s">%1$s</h1>',
                single_term_title('', false), esc_attr(self::$taxonomy)
            );

            return $title;
        }

        $styles = [
            'background-color:' . $options['color'],
            'color:' . self::get_text_color($options['color'])
        ];

        $title = sprintf(
            '<h1 class="tagline-title tagline-title--%2$s" style="%3$s">%1$s</h1>',
            single_term_title('', false), esc_attr(self::$taxonomy),
            esc_attr(implode('; ', $styles))
        );

        return $title;
    }


    /**
     * Get text color using relative luminance
     *
     * @link https://en.wikipedia.org/wiki/Relative_luminance
     * @since 1.9
     */
    private static function get_text_color($color) {
        $color = trim($color, '#');

        if(strlen($color) == 3) {
            $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
            $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
            $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
        } elseif(strlen($color) == 6) {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        }

        // Get relative luminance
        $y = 0.2126*$r + 0.7152*$g + 0.0722*$b;

        return $y > 128 ? '#000' : '#fff';
    }
}


/**
 * Load current module environment
 */
Knife_Special_Projects::load_module();
