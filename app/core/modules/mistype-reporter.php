<?php
/**
* Mistype reporter
*
* Send typo errors to telegram private channel
*
* @package knife-theme
* @since 1.12
* @version 1.17
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Knife_Mistype_Reporter {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Include mistype reporter settings
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inject_object' ), 12 );

        if ( ! defined( 'KNIFE_REQUESTS' ) ) {
            define( 'KNIFE_REQUESTS', array() );
        }
    }

    /**
     * Append user form to page content
     */
    public static function inject_object() {
        $secret = empty( KNIFE_REQUESTS['secret'] ) ? '' : KNIFE_REQUESTS['secret'];

        // Get current time stamp
        $timestamp = time();

        $options = array(
            'ajaxurl'  => '/requests',
            'nonce'    => substr( sha1( $secret . $timestamp ), -12, 10 ),
            'time'     => $timestamp,
            'button'   => esc_html__( 'Отправить', 'knife-theme' ),
            'heading'  => esc_html__( 'Сообщить об ошибке', 'knife-theme' ),
            'textarea' => esc_html__( 'Комментарий', 'knife-theme' ),
        );

        // add user form fields
        wp_localize_script( 'knife-theme', 'knife_mistype_reporter', $options );
    }
}


/**
 * Load module
 */
Knife_Mistype_Reporter::load_module();
