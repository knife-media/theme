<?php
/**
* Template engine
*
* Manage theme templates including
*
* @package knife-theme
* @since 1.3
*/


if (!defined('WPINC')) {
    die;
}


new Knife_Template_Engine;

class Knife_Template_Engine {
    /**
     * Main template function using instead of default get_template_part
     */
    public function get_template($slug, $name = null) {
        $templates = [];

        /**
         * Fires before the specified template part file is loaded.
         *
         * @since 1.3
         * @param string $slug
         * @param string|null $name
         */
        do_action('knife_template', $slug, $name);

        // Get custom name according class method
        $method = 'template_' . $slug;

        if($name === null && method_exists(__CLASS__, $method)) {
            $name = $this->$method();
        }

        // If name exists, return template array
        if((string) $name !== '') {
            $templates[] = "templates/{$slug}-{$name}.php";
        }

        // Default template part by slug
        $templates[] = "templates/{$slug}.php";

        return $templates;
    }


    /**
     * Get content template part
     */
    private function template_content() {
        $template = get_post_type();

        if(post_type_supports($template, 'post-formats')) {
            $template = get_post_format();
        }

        /**
         * Filters content template
         *
         * @since 1.3
         * @param string $template
         */
        return apply_filters('knife_template_content', $template);
    }


    /**
     * Get content template part
     */
    private function template_archive() {
        $template = null;

        /**
         * Filters archive template
         *
         * @since 1.3
         * @param string $template
         */
        return apply_filters('knife_template_archive', $template);
    }
}
