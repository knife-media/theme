<?php
/**
 * Custom Knife template tags
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package knife-theme
 * @since 1.1
 */


if(!function_exists('the_template')) :
    /**
     * Public function for flexible replace default get_template_part function
     *
     * @since 1.3
     */
    function the_template($slug, $name = null) {
        $templates = (new Knife_Template_Engine)->get_template($slug, $name);

        locate_template($templates, true, false);
    }
endif;


if(!function_exists('the_share')) :
    /**
     * Public function using on templates to get current post lead text
     *
     * @since 1.3
     */
    function the_share($before = '', $after = '', $action = '', $title = '', $echo = true) {
        $share = (new Knife_Share_Buttons)->get_buttons($action, $title);

        $output = $before . $share . $after;

        if($echo === true)
            echo $output;

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

        if($echo === true)
            echo $output;

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

        if($lead === false)
            return;

        $output = $before . $lead . $after;

        if($echo === true)
            echo $output;

        return $output;
    }
endif;


if(!function_exists('the_story')) :
    /**
     * Show story slides
     *
     * @since 1.3
     */
    function the_story($before = '', $after = '', $echo = true) {
        $output = (new Knife_Story_Manager)->get_story($before, $after);

        if($echo === true)
            echo $output;

        return $output;
    }
endif;
