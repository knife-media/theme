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
        // Common filters
        add_filter('embed_defaults', [__CLASS__, 'set_defaults']);
        add_filter('embed_oembed_html', [__CLASS__, 'append_markup'], 12, 3);

        // Instagram update
        add_filter('oembed_providers', [__CLASS__, 'hide_instagram_caption']);
        add_filter('embed_oembed_html', [__CLASS__, 'move_instagram_script'], 10, 4);
        add_filter('script_loader_tag', [__CLASS__, 'update_instagram_loader'], 10, 3);

        // YouTube preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_youtube_embed'], 10, 2);

        // Vimeo preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_vimeo_embed'], 10, 2);
    }


    /**
     * Set default embed sizes
     *
     * @since 1.5
     */
    public static function set_defaults() {
        return ['width' => 640, 'height' => 525];
    }


    /**
     * Append figure wrapper to embeds
     *
     * @since 1.5
     */
    public static function append_markup($html, $url, $attr) {
        $html = '<figure class="figure figure--embed">' . $html . '</figure>';

        return $html;
    }


    /**
     * Remove instagram embeds caption
     */
    public static function hide_instagram_caption($providers) {
        $providers['#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i'] = array('https://api.instagram.com/oembed?hidecaption=true', true);

        return $providers;
    }


    /**
     * Remove multiple js script from embeds and insert single with enqueue
     *
     * @since 1.5
     */
    public static function move_instagram_script($cache, $url, $attr, $post_id) {
        if(preg_match('#https?://(www\.)?instagr(\.am|am\.com)/p/.*#i', $url)) {
            wp_enqueue_script('instagram-embed', 'https://www.instagram.com/embed.js', [], null, true);

            $cache = str_replace('<script async src="//www.instagram.com/embed.js"></script>', '', $cache);
        }

        return $cache;
    }


    /**
     * Add async and defer atts to instagram loader tag
     */
    public static function update_instagram_loader($tag, $handle, $src) {
        if($handle === 'instagram-embed') {
            $tag = str_replace('<script', '<script async ', $tag);
        }

        return $tag;
    }


    /**
     * Update YouTube html to show preloader
     *
     * @since 1.5
     */
    public static function update_youtube_embed($result, $url) {
        if(preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $preview = "https://img.youtube.com/vi/{$match[1]}/default.jpg";

            foreach(['maxresdefault', 'hqdefault', 'mqdefault'] as $size) {
                $response = wp_remote_head("https://img.youtube.com/vi/{$match[1]}/{$size}.jpg");

                if(wp_remote_retrieve_response_code($response) === 200) {
                    $preview = "https://img.youtube.com/vi/{$match[1]}/{$size}.jpg";

                    break;
                }
            }

            $result = sprintf(
                '<div class="embed embed--youtube" data-embed="%1$s">%2$s</div>',
                esc_attr($match[1]),
/*              sprintf(
                    '<a class="embed__dummy" href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
 */
                '<div class="embed__loader"><span class="embed__loader-bounce"></span></div>'
            );
        }

        return $result;
    }


    /**
     * Update Vimeo html to show preloader
     *
     * @since 1.5
     */
    public static function update_vimeo_embed($result, $url) {
        if(preg_match('%https?://(?:.+\.)?vimeo\.com/([\d]+).*%i', $url, $match)) {

            $request = wp_remote_get("https://vimeo.com/api/v2/video/{$match[1]}.json");

            if(is_wp_error( $request ) ) {
                return false; // Bail early
            }
$body = wp_remote_retrieve_body( $request );
$data = json_decode( $body );


            $preview = "https://img.youtube.com/vi/{$match[1]}/default.jpg";

            foreach(['maxresdefault', 'hqdefault', 'mqdefault'] as $size) {
                $response = wp_remote_head("https://img.youtube.com/vi/{$match[1]}/{$size}.jpg");

                if(wp_remote_retrieve_response_code($response) === 200) {
                    $preview = "https://img.youtube.com/vi/{$match[1]}/{$size}.jpg";

                    break;
                }
            }

            $result = sprintf(
                '<div class="embed-youtube" data-embed="%1$s">%2$s</div>',
                esc_attr($match[1]),
                sprintf(
                    '<a href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
            );
        }

        return $result;
    }

}


/**
 * Load current module environment
 */
Knife_Embed_Filters::load_module();
