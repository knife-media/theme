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

        if(in_array($name, KNIFE_CUSTOMS)) {
            $functions = "/core/customs/{$name}/functions.php";

            // Let's add the file if exists
            if(file_exists(get_template_directory() .  $functions)) {
                // Require customs
                require_once(get_template_directory() .  $functions);
            }
        }
    }
}


/**
 * Load current module environment
 */
Knife_Post_Customs::load_module();
