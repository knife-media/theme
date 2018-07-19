<?php
/**
* Embed filters
*
* Customize default wordpress embed code
*
* @package knife-theme
* @since 1.2
*/

if (!defined('WPINC')) {
    die;
}


(new Knife_Embed_Filters)->init();

class Knife_Embed_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public function init() {
        // Instagram update
        add_filter('oembed_providers', [$this, 'instagram_api']);
        add_filter('embed_oembed_html', [$this, 'instagram_script'], 10, 4);
        add_filter('script_loader_tag', [$this, 'instagram_loader'], 10, 3);
    }


    /**
     * Remove instagram embeds caption
     */
    public function instagram_api($providers) {
        $providers['#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'] = array('https://api.instagram.com/oembed?hidecaption=true', true);

        return $providers;
    }


    /**
     * Remove multiple js script from embeds and insert single with enqueue
     */
    public function instagram_script($cache, $url, $attr, $post_id) {
        if(!preg_match('#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i', $url)) {
            return $cache;
        }

        wp_enqueue_script('instagram-embed', 'https://www.instagram.com/embed.js', [], null, true);

        return str_replace('<script async defer src="//www.instagram.com/embed.js"></script>', '', $cache);
    }


    /**
     * Add async and defer atts to instagram loader tag
     */
    public function instagram_loader($tag, $handle, $src) {
        if ($handle !== 'instagram-embed') {
            return $tag;
        }

        return str_replace('<script', '<script async defer', $tag);
    }
}
