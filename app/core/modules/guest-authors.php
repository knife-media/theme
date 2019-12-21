<?php
/**
 * Guest authors
 *
 * Allow mutiple guest authors per post
 *
 * @package knife-theme
 * @since 1.11
 */

if (!defined('WPINC')) {
    die;
}


class Knife_Guest_Authors {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
    }
}


/**
 * Load current module environment
 */
Knife_Guest_Authors::load_module();
