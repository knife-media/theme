<?php
/**
 * Post customs
 *
 * Add checkbox to determine posts with custom modifications
 *
 * @package knife-theme
 * @since 1.12
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Post_Customs {
    /**
     * Post meta to store adult content option
     *
     * @access  public
     * @var     string
     */
    public static $meta_customs = '_knife-post-customs';


   /**
     * Default post type lead text availible
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Add option to show customs
        add_action('post_submitbox_misc_actions', [__CLASS__, 'print_checkbox'], 15);

        // Update adult posts meta
        add_action('save_post', [__CLASS__, 'save_meta']);

        // Process custom posts ajax requests
        if(wp_doing_ajax()) {
            add_action('init', [__CLASS__, 'process_ajax']);
        }

        // Load custom post functions
        add_action('wp', [__CLASS__, 'load_functions']);
    }


    /**
     * Process custom posts ajax requests
     */
    public static function process_ajax() {
        $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';

        if(strpos($action, 'knife-customs-') === 0) {
            $name = str_replace('knife-customs-', '', $action);

            // Get custom post functions file
            $functions = "/core/customs/{$name}/functions.php";

            // Let's add the file if exists
            if(file_exists(get_template_directory() .  $functions)) {
                // Require ajax customs
                require_once(get_template_directory() .  $functions);
            }
        }
    }


    /**
     * Load custom post functions
     */
    public static function load_functions() {
        if(!is_singular(self::$post_type)) {
            return;
        }

        $object = get_queried_object();

        // Get post name
        $name = $object->post_name;

        if(get_post_meta($object->ID, self::$meta_customs, true)) {
            $functions = "/core/customs/{$name}/functions.php";

            // Let's add the file if exists
            if(file_exists(get_template_directory() .  $functions)) {
                // Require customs
                require_once(get_template_directory() .  $functions);
            }
        }
    }


    /**
     * Prints checkbox in post publish action section
     */
    public static function print_checkbox($post) {
        if(!in_array($post->post_type, self::$post_type)) {
            return;
        }

        $customs = get_post_meta($post->ID, self::$meta_customs, true);

        printf(
            '<div class="misc-pub-section"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></div>',
            esc_attr(self::$meta_customs),
            __('Загружать модификации поста', 'knife-theme'),
            checked($customs, 1, false)
        );
    }


    /**
     * Save post meta
     */
    public static function save_meta($post_id) {
        if(!in_array(get_post_type($post_id), self::$post_type)) {
            return;
        }

        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if(!current_user_can('edit_post', $post_id)) {
            return;
        }

        if(empty($_REQUEST[self::$meta_customs])) {
            return delete_post_meta($post_id, self::$meta_customs);
        }

        return update_post_meta($post_id, self::$meta_customs, 1);
    }
}


/**
 * Load current module environment
 */
Knife_Post_Customs::load_module();
