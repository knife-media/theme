<?php
/**
 * Yandex RTB
 *
 * Set Yandex RTB options
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.7
 */


if (!defined('WPINC')) {
    die;
}

class Knife_Yandex_RTB {
    /**
     * Option to store rtb map
     *
     * @access  private
     * @var     string
     */
    private static $option_id = 'knife_rtb_id';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        // Pass Yandex RTB options to js
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_object'], 12);

        // Plugin settings
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);
    }


    /**
     * Pass Yandex RTB options to js
     */
    public static function inject_object() {
        if(is_singular('post')) {
            $is_promo = get_post_meta(get_the_ID(), '_knife-promo', true);

            // Check if promo or special post
            if(has_term('', 'special') || $is_promo) {
                return;
            }

            $yandex_rtb = get_theme_mod(self::$option_id);

            if(strlen($yandex_rtb) > 0) {
                wp_localize_script('knife-theme', 'knife_yandex_rtb', $yandex_rtb);
            }
        }
    }


    /**
     * Yandex RTB Customizer options
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$option_id);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$option_id, [
                 'label' => __('Блок РСЯ под постом', 'knife-theme'),
                 'section' => 'title_tagline',
                 'input_attrs' => [
                     'placeholder' => __('R-A-265990-0', 'knife-theme')
                 ]
             ]
        ));
    }
}


/**
 * Load current module environment
 */
Knife_Yandex_RTB::load_module();
