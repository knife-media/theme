<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.10
 */


if(!function_exists('the_share')) :
    /**
     * Public function using on templates to show share buttons
     *
     * @since 1.3
     */
    function the_share($before = '', $after = '', $action = '', $echo = true, $output = '') {
        if(method_exists('Knife_Share_Buttons', 'get_buttons')) {
            $output = Knife_Share_Buttons::get_buttons($action);
        }

        // Check if output not empty
        if(strlen($output) > 0) {
            $output = $before . $output . $after;

            if($echo === false) {
                return $output;
            }

            echo $output;
        }
    }
endif;


if(!function_exists('the_info')) :
    /**
     * Public function using on templates to show post info
     *
     * @since 1.3
     */
    function the_info($before = '', $after = '', $options = [], $echo = true, $output = '') {
        // Append post meta
        if(method_exists('Knife_Post_Info', 'get_info')) {
            $output = Knife_Post_Info::get_info($options);
        }

        // Check if output not empty
        if(strlen($output) > 0) {
            $output = $before . $output . $after;

            if($echo === false) {
                return $output;
            }

            echo $output;
        }
    }
endif;


if(!function_exists('the_lead')) :
    /**
     * Public function using on templates to get current post lead text
     *
     * @since 1.3
     */
    function the_lead($before = '', $after = '', $echo = true, $output = '') {
        if(method_exists('Knife_Post_Lead', 'get_lead')) {
            $output = Knife_Post_Lead::get_lead();
        }

        // Check if output not empty
        if(strlen($output) > 0) {
            $output = $before . $output . $after;

            if($echo === false) {
                return $output;
            }

            echo $output;
        }
    }
endif;


if(!function_exists('the_tagline')) :
    /**
     * Public function using on templates to get tagline
     *
     * @since 1.10
     */
    function the_tagline($before = '', $after = '', $echo = true, $output = '') {
        if(method_exists('Knife_Post_Info', 'get_tagline')) {
            $output = Knife_Post_Info::get_tagline();
        }

        // Check if output not empty
        if(strlen($output) > 0) {
            $output = $before . $output . $after;

            if($echo === false) {
                return $output;
            }

            echo $output;
        }
    }
endif;
