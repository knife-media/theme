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
        // Process custom posts ajax requests
        if(wp_doing_ajax()) {
            add_action('init', [__CLASS__, 'process_ajax']);
        }

        // Load custom post functions
        add_action('wp', [__CLASS__, 'load_functions']);

        // Define post customs settings if still not
        if(!defined('KNIFE_CUSTOMS')) {
            define('KNIFE_CUSTOMS', []);
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

        if(array_key_exists($name, KNIFE_CUSTOMS)) {
            $functions = "/core/customs/{$name}/functions.php";

            // Let's add the file if exists
            if(file_exists(get_template_directory() .  $functions)) {
                // Require customs
                require_once(get_template_directory() .  $functions);
            }
        }
    }


    /**
     * Process custom posts ajax requests
     */
    public static function process_ajax() {
        $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';

        if(strpos($action, 'knife-customs-') !== 0) {
            return;
        }

        $name = str_replace('knife-customs-', '', $action);

        // Get custom post functions file
        $ajax = "/core/customs/{$name}/ajax.php";

        // Let's add the file if exists
        if(file_exists(get_template_directory() . $ajax)) {
            // Require ajax customs
            add_action('wp_ajax_' . $action, function() use ($action, $ajax) {
                if(!check_ajax_referer($action, 'nonce', false)) {
                    wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
                }

                require(get_template_directory() . $ajax);
            });

            add_action('wp_ajax_nopriv_' . $action, function() use ($action, $ajax) {
                if(!check_ajax_referer($action, 'nonce', false)) {
                    wp_send_json_error(__('Ошибка безопасности. Попробуйте еще раз', 'knife-theme'));
                }

                require(get_template_directory() . $ajax);
            });
        }
    }
}


/**
 * Load current module environment
 */
Knife_Post_Customs::load_module();
