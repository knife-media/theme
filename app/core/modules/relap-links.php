<?php
/**
* Relap links
*
* Set relap links options
*
* @package knife-theme
* @since 1.5
*/


if (!defined('WPINC')) {
    die;
}

class Knife_Relap_Links {
    /**
     * Option to store relap token
     *
     * @access  private
     * @var     string
     */
    private static $relap_token = 'knife_relap_token';


    /**
     * Option to store relap block id
     *
     * @access  private
     * @var     string
     */
    private static $relap_block = 'knife_relap_block';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Pass Relap links options to js
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Plugin settings
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);
    }


    /**
     * Pass Relap links options to js
     */
    public static function inject_object() {
        if(is_singular('post')) {
            $relap_options = [
                'token' => get_theme_mod(self::$relap_token),
                'block' => get_theme_mod(self::$relap_block)
            ];

            wp_localize_script('knife-theme', 'knife_relap_options', $relap_options);
        }
    }


    /**
     * Save Relap options
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$relap_token);
        $wp_customize->add_setting(self::$relap_block);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$relap_token, [
                 'label'      => __('Relap token', 'knife-theme'),
                 'section'    => 'title_tagline'
             ]
        ));

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$relap_block, [
                 'label'      => __('Relap ID виджета', 'knife-theme'),
                 'section'    => 'title_tagline'
             ]
        ));
    }

}


/**
 * Load current module environment
 */
Knife_Relap_Links::load_module();
