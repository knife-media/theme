<?php
/**
 * Relap links
 *
 * Set relap links options
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.9
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Relap_Links {
   /**
    * Post types relap availible
    *
    * @access  private
    * @var     array
    */
    private static $post_type = ['post', 'generator', 'quiz'];


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Pass Relap links options to js
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Define short links settings if still not
        if(!defined('KNIFE_RELAP')) {
            define('KNIFE_RELAP', []);
        }
    }


    /**
     * Pass Relap links options to js
     */
    public static function inject_object() {
        if(is_singular(self::$post_type) && !has_term('', 'special')) {
            // Mix with default values
            $conf = wp_parse_args(KNIFE_RELAP, [
                'block' => '',
                'token' => ''
            ]);

            wp_localize_script('knife-theme', 'knife_relap_options', $conf);
        }
    }
}


/**
 * Load current module environment
 */
Knife_Relap_Links::load_module();
