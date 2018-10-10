<?php
/**
* Embed filters
*
* Customize default wordpress embed code
*
* @package knife-theme
* @since 1.2
* @version 1.5
*/

if (!defined('WPINC')) {
    die;
}


class Knife_Embed_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public static function load_module() {
        // Instagram update
        add_filter('oembed_providers', [__CLASS__, 'instagram_api']);
        add_filter('embed_oembed_html', [__CLASS__, 'instagram_script'], 10, 4);
        add_filter('script_loader_tag', [__CLASS__, 'instagram_loader'], 10, 3);
    }


    /**
     * Remove instagram embeds caption
     */
    public static function instagram_api($providers) {
        $providers['#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'] = array('https://api.instagram.com/oembed?hidecaption=true', true);

        return $providers;
    }


    /**
     * Remove multiple js script from embeds and insert single with enqueue
     *
     * @since 1.5
     */
    public static function instagram_script($cache, $url, $attr, $post_id) {
        if(!preg_match('#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i', $url)) {
            return $cache;
        }

        wp_enqueue_script('instagram-embed', 'https://www.instagram.com/embed.js', [], null, true);

        return str_replace('<script async src="//www.instagram.com/embed.js"></script>', '', $cache);
    }


    /**
     * Add async and defer atts to instagram loader tag
     */
    public static function instagram_loader($tag, $handle, $src) {
        if($handle !== 'instagram-embed') {
            return $tag;
        }

        return str_replace('<script', '<script async ', $tag);
    }
}


/**
 * Load current module environment
 */
Knife_Embed_Filters::load_module();
