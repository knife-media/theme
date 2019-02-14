<?php
/**
 * Embed filters
 *
 * Customize default wordpress embed code
 *
 * @package knife-theme
 * @since 1.2
 * @version 1.7
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

        // YouTube preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_youtube_embed'], 10, 2);

        // Vimeo preloader
        add_filter('pre_oembed_result', [__CLASS__, 'update_vimeo_embed'], 10, 2);

        // Replace custom video preloaders in feeds
        add_filter('the_content_feed', [__CLASS__, 'replace_embeds'], 5);
    }


    /**
     * Set default embed sizes
     *
     * @since 1.5
     */
    public static function set_defaults($defaults) {
        $defaults = [
            'width' => 640,
            'height' => 525
        ];

        return $defaults;
    }


    /**
     * Append figure wrapper to embeds
     *
     * @since 1.5
     */
    public static function append_markup($html, $url, $attr) {
        if(!is_feed()) {
            $html = '<figure class="figure figure--embed">' . $html . '</figure>';
        }

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

            $params = [
                'rel' => 0,
                'showinfo' => 0,
                'autoplay' => 1
            ];

            wp_parse_str(wp_parse_url($url, PHP_URL_QUERY), $query);

            if(isset($query['t'])) {
                $params['start'] = $query['t'];
            }

            $result = sprintf(
                '<div class="embed embed--youtube" data-embed="%1$s">%2$s</div>',
                sprintf(
                    'https://www.youtube.com/embed/%1$s?%2$s',
                    esc_attr($match[1]),
                    build_query($params)
                ),
                sprintf(
                    '<a class="embed__dummy" href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
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
        if(preg_match('%https?://(?:.+\.)?vimeo\.com/(?:video/)?([\d]+).*%i', $url, $match)) {
            $request = wp_remote_get("https://vimeo.com/api/v2/video/{$match[1]}.json");
            $preview = '';

            if(!is_wp_error($request)) {
                $meta = json_decode(wp_remote_retrieve_body($request));

                foreach(['thumbnail_large', 'thumbnail_medium', 'thumbnail_small'] as $size) {
                    if(isset($meta[0]->$size)) {
                        $preview = $meta[0]->$size;

                        break;
                    }
                }
            }

            $result = sprintf(
                '<div class="embed embed--vimeo" data-embed="%1$s">%2$s</div>',
                sprintf(
                    'https://player.vimeo.com/video/%s?byline=0&autoplay=1',
                    esc_attr($match[1])
                ),
                sprintf(
                    '<a class="embed__dummy" href="%1$s" target="_blank" style="background-image: url(%2$s)"></a>',
                    esc_url($url),
                    esc_url($preview)
                )
            );
        }

        return $result;
    }


    /**
     * Replace custom video preloaders in feeds
     *
     * @since 1.5
     */
    public static function replace_embeds($content) {
        $content = preg_replace(
            '~<div class="embed\s.+?"\s+data-embed="([^"]+)".+?</div>~is',
            '<iframe src="$1" frameborder="0"></iframe>',
            $content
        );

        return $content;
    }

}


/**
 * Load current module environment
 */
Knife_Embed_Filters::load_module();
