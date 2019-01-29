<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.7
 */


if(!function_exists('the_share')) :
    /**
     * Public function using on templates to show share buttons
     *
     * @since 1.3
     */
    function the_share($before = '', $after = '', $action = '', $echo = true) {
        if(method_exists('Knife_Share_Buttons', 'get_buttons')) {
            $share = Knife_Share_Buttons::get_buttons($action);

            $output = $before . $share . $after;

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
    function the_info($before = '', $after = '', $options = [], $echo = true) {
        if(method_exists('Knife_Post_Info', 'get_info')) {
            $info = Knife_Post_Info::get_info($options);

            $output = $before . $info . $after;

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
    function the_lead($before = '', $after = '', $echo = true) {
        if(method_exists('Knife_Post_Lead', 'get_lead')) {
            $lead = Knife_Post_Lead::get_lead();

            if(strlen($lead) > 0) {
                $output = $before . $lead . $after;

                if($echo === false) {
                    return $output;
                }

                echo $output;
            }
        }
    }
endif;
