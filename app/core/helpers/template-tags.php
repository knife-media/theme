<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */


if(!function_exists('the_share')) :
    /**
     * Public function using on templates to get current post lead text
     *
     * @since 1.3
     */
    function the_share($before = '', $after = '', $action = '', $title = '', $echo = true) {
        $share = (new Knife_Share_Buttons)->get_buttons($action, $title);

        $output = $before . $share . $after;

        if($echo === true) {
            echo $output;
        }

        return $output;
    }
endif;


if(!function_exists('the_info')) :
    /**
     * Public function using on templates to get current post lead text
     *
     * @since 1.3
     */
    function the_info($before = '', $after = '', $options = '', $echo = true) {
        $info = (new Knife_Post_Info)->get_info($options);

        $output = $before . $info . $after;

        if($echo === true) {
            echo $output;
        }

        return $output;
    }
endif;


if(!function_exists('the_lead')) :
    /**
     * Public function using on templates to get current post lead text
     *
     * @since 1.3
     */
    function the_lead($before = '', $after = '', $echo = true) {
        $lead = (new Knife_Post_Lead)->get_meta();

        if((string) $lead === '') {
            return;
        }

        $output = $before . $lead . $after;

        if($echo === true) {
            echo $output;
        }

        return $output;
    }
endif;


if(!function_exists('the_sidebar')) :
    /**
     * Public function using on templates to show sidebar with before and after
     *
     * @since 1.3
     */
    function the_sidebar($id, $before = '', $after = '', $echo = true) {
        $sidebar = (new Knife_Widget_Handler)->get_sidebar($id);

        if((string) $sidebar === '') {
            return;
        }

        $output = $before . $sidebar . $after;

        if($echo === true) {
            echo $output;
        }

        return $output;
    }
endif;
