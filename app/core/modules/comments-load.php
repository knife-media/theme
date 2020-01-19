<?php
/**
 * Hypercomments handler
 *
 * Set Hypercomments script options
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.12
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Comments_Load {
    /**
     * Option to store search settings
     *
     * @access  private
     * @var     string
     */
    private static $comments_id = 'knife_comments_id';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Pass Hypercomments id to js
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Define comments settings
        if(!defined('KNIFE_COMMENTS')) {
            define('KNIFE_COMMENTS', []);
        }
    }


    /**
     * Pass Hypercomments id to js
     */
    public static function inject_object() {
        if(!empty(KNIFE_COMMENTS['id'])) {
            wp_localize_script('knife-theme', 'knife_comments_id', KNIFE_COMMENTS['id']);
        }
    }
}


/**
 * Load current module environment
 */
Knife_Comments_Load::load_module();
