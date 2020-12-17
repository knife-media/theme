<?php
/**
 * sharing-community: custom functions
 *
 * @package knife-theme
 * @since 1.14
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Remove promo tag from footer
 */
add_action('wp', function() {
    if(class_exists('Knife_Promo_Manager')) {
        remove_filter('term_links-post_tag', ['Knife_Promo_Manager', 'add_promo_tag']);
    }
}, 15);
