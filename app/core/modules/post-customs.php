<?php
/**
 * Post customs
 *
 * Add checkbox to determine posts with custom modifications
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.14
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Post_Customs {
    /**
     * Post types with customs
     *
     * @access  public
     * @var     array
     */
    public static $post_type = ['post', 'page', 'quiz', 'generator'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Load custom post functions
        add_action('wp', [__CLASS__, 'load_functions']);
    }


    /**
     * Load custom post functions only for current post name
     */
    public static function load_functions() {
        if(!is_singular(self::$post_type)) {
            return;
        }

        $object = get_queried_object();

        // Get current post name
        $include = get_template_directory() . "/core/custom/" . $object->post_name;

        // Let's add the file if exists
        if(file_exists($include . '/functions.php')) {
            include_once $include . '/functions.php';
        }
    }
}


/**
 * Load current module environment
 */
Knife_Post_Customs::load_module();
