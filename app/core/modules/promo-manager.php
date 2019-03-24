<?php
/**
 * Promo manager
 *
 * Promo posts classification with custom possibility
 *
 * @package knife-theme
 * @since 1.8
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

        // Add share query tag
        add_action('query_vars', [__CLASS__, 'append_promo_var']);

        // Include archive template for promo posts
        add_filter('template_include', [__CLASS__, 'include_archive']);

        // Show posts only with promo post meta
        add_action('pre_get_posts', [__CLASS__, 'update_query']);

        // Update promo archive document title
        add_filter('document_title_parts', [__CLASS__, 'update_title'], 10);

        // Set is-promo class if need
        add_filter('body_class', [__CLASS__, 'set_body_class'], 11);

        // Promo checkbox
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox']);

        // Update promo post meta on save post
        add_action('save_post', [__CLASS__, 'save_metabox']);
    }


    /**
     * Output promo post info
     */
    public static function get_info($options = [], $output = '') {
        if(in_array('promo', $options)) {
            $promo = get_post_meta(get_the_ID(), self::$meta_promo, true);

            if((string) $promo === '1') {
                $output = sprintf('<span class="meta__promo">%s</span>',
                    __('Партнерский материал', 'knife-theme')
                );

                // Show link tp archive for editors
                if(current_user_can('publish_posts')) {
                    $output = sprintf('<a href="%s" class="meta__promo">%s</span>',
                        trailingslashit(home_url(self::$query_var)),
                        __('Партнерский материал', 'knife-theme')
                    );
                }
            }
        }

        return $output;
    }


    /**
     * Create custom promo archive url
     */
    public static function add_promo_rule() {
        add_rewrite_rule(
            sprintf(
                '^%s/?$', self::$query_var
            ),

            sprintf(
                'index.php?%s=1', self::$query_var
            ),

            'top'
        );
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
            $query->set('meta_key', self::$meta_promo);
            $query->set('meta_value', 1);
        }
    }


    /**
     * Update promo archive document title
     */
    public static function update_title($title) {
        if(get_query_var(self::$query_var)) {
            $title['title'] = __('Партнерские материалы', 'knife-theme');
        }

        return $title;
    }



    /**
     * Print checkbox in post publish action section
     */
    public static function print_checkbox() {
        $post_id = get_the_ID();

        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        $promo = get_post_meta($post_id, self::$meta_promo, true);

        printf(
            '<div class="misc-pub-section misc-pub-section-last"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_promo),
            __('Партнерский материал', 'knife-theme'),
            checked($promo, 1, false)
        );


        wp_nonce_field('checkbox', self::$metabox_nonce);
    }


    /**
     * Save promo post meta
     */
    public static function save_metabox($post_id) {
        if(!isset($_REQUEST[self::$metabox_nonce])) {
            return;
        }

        if(!wp_verify_nonce($_REQUEST[self::$metabox_nonce], 'checkbox')) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_promo])) {
            return delete_post_meta($post_id, self::$meta_promo);
        }

        return update_post_meta($post_id, self::$meta_promo, 1);
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
}


/**
 * Load current module environment
 */
Knife_Promo_Manager::load_module();
