<?php
/**
 * Promo manager
 *
 * Promo posts classification with custom possibility
 *
 * @package knife-theme
 * @since 1.8
 * @version 1.10
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Promo_Manager {
    /**
     * Default post type with promo checkbox
     *
     * @access  private
     * @var     array
     */
    private static $post_type = ['post', 'club', 'quiz'];


    /**
     * Unique meta to indicate if post promoted
     *
     * @access  private
     * @var     string
     */
    private static $meta_promo = '_knife-promo';


    /**
     * Unique meta to store promo options
     *
     * @access  private
     * @var     string
     */
    private static $meta_options = '_knife-promo-options';


    /**
     * Archive query var
     *
     * @access  private
     * @var     string
     */
    private static $query_var = 'promo';


    /**
     * Checkbox save nonce
     *
     * @access  private
     * @var     string
     */
    private static $metabox_nonce = 'knife-promo-nonce';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Create custom promo archive url
        add_action('init', [__CLASS__, 'add_promo_rule']);

        // Update query_vars for promo archive
        add_action('parse_query', [__CLASS__, 'update_query_vars']);

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_promo_var']);

        // Include archive template for promo posts
        add_filter('template_include', [__CLASS__, 'include_archive']);

        // Show posts only with promo post meta
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update archive caption title
        add_filter('get_the_archive_title', [__CLASS__, 'update_archive_title'], 12);

        // Update promo archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_document_title'], 10);

        // Set is-promo class if need
        add_filter('body_class', [__CLASS__, 'set_body_class'], 11);

        // Add snippet image metabox
        add_action('add_meta_boxes', [__CLASS__, 'add_metabox']);

        // Update promo post meta on save post
        add_action('save_post', [__CLASS__, 'save_metabox']);

        // Enqueue metabox scripts
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // Add promo tag to tag list
        add_filter('term_links-post_tag', [__CLASS__, 'add_promo_tag']);
    }


    /**
     * Get promo board markup
     */
    public static function get_promo($output = '') {
        $post_id = get_the_ID();

        // Check if post promoted
        if(!get_post_meta($post_id, self::$meta_promo, true)) {
            return $output;
        }

        // Get promo options
        $options = (array) get_post_meta($post_id, self::$meta_options, true);

        // Set default promo panel color
        if(empty($options['color'])) {
            $options['color'] = '#fff';
        }

        // Get promo parts
        $output = self::compose_promo($options);

        return $output;
    }


    /**
     * Create custom promo archive url
     */
    public static function add_promo_rule() {
        add_rewrite_rule(
            sprintf('^%s/?$', self::$query_var),
            sprintf('index.php?%s=1', self::$query_var),
            'top'
        );

        add_rewrite_rule(
            sprintf('^%s/page/([0-9]+)/?$', self::$query_var),
            sprintf('index.php?%s=1&paged=$matches[1]', self::$query_var),
            'top'
        );
    }


    /**
     * Update query_vars for promo archive
     */
    public static function update_query_vars() {
        global $wp_query;

        if(array_key_exists(self::$query_var, $wp_query->query_vars)) {
            $wp_query->is_archive = true;
            $wp_query->is_home = false;
        }
    }


    /**
     * Append promo query tag to availible query vars
     */
    public static function append_promo_var($query_vars) {
        $query_vars[] = self::$query_var;

        return $query_vars;
    }


    /**
     * Include archive.php template for promo posts
     */
    public static function include_archive($template) {
        if(get_query_var(self::$query_var)) {
            $new_template = locate_template(['archive.php']);

            if(!empty($new_template)) {
                return $new_template;
            }
        }

        return $template;
    }


    /**
     * Show only promo posts in custom archive template
     */
    public static function update_query($query) {
        if(is_admin() || !$query->is_main_query()) {
            return false;
        }

        if(get_query_var(self::$query_var)) {
            $query->set('post_type', 'any');
            $query->set('meta_key', self::$meta_promo);
            $query->set('meta_value', 1);
        }
    }


    /**
     * Update post archive caption title
     */
    public static function update_archive_title($title) {
        if(get_query_var(self::$query_var)) {
            $title = sprintf('<h1 class="caption__title">%s</h1>',
                __('Партнерские материалы', 'knife-theme')
            );
        }

        return $title;
    }


    /**
     * Update promo archive document title
     */
    public static function update_document_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Партнерские материалы', 'knife-theme');
        }

        return $title;
    }


    /**
     * Add promo tag to tag list
     *
     * @since 1.9
     */
    public static function add_promo_tag($tags) {
        $is_promo = get_post_meta(get_the_ID(), self::$meta_promo, true);

        if($is_promo) {
            $link = sprintf('<a href="%s" rel="tag">%s</a>',
                esc_url(home_url(self::$query_var)),
                __('партнерский материал', 'knife-theme')
            );

            array_unshift($tags, $link);
        }

        return $tags;
    }


    /**
     * Add snippet image metabox
     */
    public static function add_metabox() {
        add_meta_box('knife-promo-metabox', __('Настройки промо', 'knife-theme'), [__CLASS__, 'display_metabox'], self::$post_type, 'side');
    }


    /**
     * Enqueue assets for metabox
     */
    public static function enqueue_assets($hook) {
        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        if(in_array($hook, ['post.php', 'post-new.php'])) {
            $post_id = get_the_ID();

            // Current screen object
            $screen = get_current_screen();

            if(!in_array($screen->post_type, self::$post_type)) {
                return;
            }

            // Insert color picker scripts
            wp_enqueue_style('wp-color-picker');

            // Insert admin scripts
            wp_enqueue_script('knife-promo-metabox', $include . '/scripts/promo-metabox.js', ['jquery', 'wp-color-picker'], $version);
        }
    }


    /**
     * Display feed metabox
     */
    public static function display_metabox() {
        $include = get_template_directory() . '/core/include';

        include_once($include . '/templates/promo-metabox.php');
    }


    /**
     * Save promo post meta
     */
    public static function save_metabox($post_id) {
        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'metabox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update options
        if(isset($_REQUEST[self::$meta_options])) {
            $options = $_REQUEST[self::$meta_options];

            // Add # to color if not exists
            if(!empty($options['color'])) {
                $options['color'] = sanitize_hex_color('#' . ltrim($options['color'], '#'));
            }

            update_post_meta($post_id, self::$meta_options, $options);
        }

        // Save promo meta
        if(empty($_REQUEST[self::$meta_promo])) {
            return delete_post_meta($post_id, self::$meta_promo);
        }

        update_post_meta($post_id, self::$meta_promo, 1);
    }


    /**
     * Set is-promo body class
     */
    public static function set_body_class($classes = []) {
        if(is_singular(self::$post_type)) {
            $post_id = get_the_ID();

            if(get_post_meta($post_id, self::$meta_promo, true)) {
                $classes[] = 'is-promo';
            }
        }

        return $classes;
    }


    /**
     * Affiliate helper method
     *
     * @since 1.9
     */
    private static function compose_promo($options, $partner = '') {
        $classes = 'promo';

        // Add logo if exists
        if(!empty($options['logo'])) {
            $partner = $partner . sprintf(
                '<img class="promo__partner-logo" src="%s" alt="">',
                esc_url($options['logo'])
            );

            $classes = $classes . ' promo--logo';
        }

        // Add title if exists
        if(!empty($options['title'])) {
            $partner = $partner . sprintf(
                '<span class="promo__partner-title">%s</span>',
                sanitize_text_field($options['title'])
            );

            $classes = $classes . ' promo--title';
        }

        // Add required title
        if(empty($options['text'])) {
            $options['text'] = __('Партнерский материал', 'knife-theme');
        }

        $promo = sprintf(
            '<span class="promo__text">%s</span>',
            sanitize_text_field($options['text'])
        );

        // Wrap logo and title
        if(!empty($partner)) {
            $promo = $promo . sprintf(
                '<div class="promo__partner">%s</div>', $partner
            );
        }

        $styles = [
            'background-color:' . $options['color'],
            'color:' . self::get_text_color($options['color'])
        ];

        $styles = implode('; ', $styles);

        // Return if link not defined
        if(empty($options['link'])) {
            $output = sprintf(
                '<div class="%2$s" style="%3$s">%1$s</div>',
                $promo, $classes, esc_attr($styles)
            );

            return $output;
        }

        $output = sprintf(
            '<a href="%2$s" class="%3$s" target="_blank" rel="noopener" style="%4$s">%1$s</a>',
            $promo, esc_url($options['link']),
            $classes, esc_attr($styles)
        );

        return $output;
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
Knife_Promo_Manager::load_module();
