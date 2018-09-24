<?php
/**
* Hypercomments handler
*
* Set Hypercomments script options
*
* @package knife-theme
* @since 1.4
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

        // Plugin settings
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);
    }


    /**
     * Pass Hypercomments id to js
     */
    public static function inject_object() {
        $comments_id = get_theme_mod(self::$comments_id);

        if(!empty($comments_id)) {
            wp_localize_script('knife-theme', 'knife_comments_id', $comments_id);
        }
    }


    /**
     * Save Hypercomments id to theme option
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$comments_id);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$comments_id, [
                 'label'      => __('Hypercomments ID', 'knife-theme'),
                 'section'    => 'title_tagline'
             ]
        ));
    }

}


/**
 * Load current module environment
 */
Knife_Comments_Load::load_module();
