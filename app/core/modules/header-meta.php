<?php
/**
* Add required social meta to blog header
*
* @package knife-theme
* @since 1.2
*/


if (!defined('WPINC')) {
    die;
}


(new Knife_Header_Meta)->init();

class Knife_Header_Meta {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     *
     * @since 1.3
     */
    public function init() {
        add_action('wp_head', [$this, 'add_meta'], 5);
        add_action('wp_head', [$this, 'add_icon'], 4);

        add_filter('language_attributes', [$this, 'add_xmlns']);
    }


    public function add_icon() {
        $meta = [];

        $path = get_template_directory_uri() . '/assets/images';

        $meta[] = sprintf('<link rel="icon" type="image/png" sizes="32x32" href="%s" />',
            $path . '/icon-32.png'
        );

         $meta[] = sprintf('<link rel="icon" type="image/png" sizes="192x192" href="%s" />',
            $path . '/icon-192.png'
        );

        $meta[] = sprintf('<link rel="apple-touch-icon" sizes="180x180" href="%s" />',
            $path . '/icon-180.png'
        );

         foreach($meta as $tag) {
            echo "$tag\n";
        }
    }


    public function add_xmlns($output) {
        return 'prefix="og: http://ogp.me/ns#" ' . $output;
    }


    public function add_meta() {
        $meta = $this->common_meta();

        $meta = (is_singular() && !is_front_page()) ? $this->single_meta($meta) : $this->archive_meta($meta);

        foreach($meta as $tag) {
            echo "$tag\n";
        }
    }


    private function single_meta($meta = []) {
        $post_id = get_queried_object_id();

        $cover = $this->get_cover($post_id);

        $meta[] = sprintf('<meta name="description" content="%s">',
            get_the_excerpt($post_id)
        );

        $meta[] = '<meta property="og:type" content="article" />';

        $meta[] = sprintf('<meta property="og:url" content="%s" />',
            get_permalink()
        );

        $meta[] = sprintf('<meta property="og:title" content="%s" />',
            strip_tags(get_the_title())
        );

        $meta[] = sprintf('<meta property="og:description" content="%s" />',
            get_the_excerpt($post_id)
        );

        $meta[] = sprintf('<meta property="og:image" content="%s" />',
            esc_url($cover[0])
        );

        $meta[] = sprintf('<meta property="og:image:width" content="%s" />',
            esc_attr($cover[1])
        );

        $meta[] = sprintf('<meta property="og:image:height" content="%s" />',
            esc_attr($cover[2])
        );

        $meta[] = sprintf('<meta property="twitter:title" content="%s" />',
            strip_tags(get_the_title())
        );

        $meta[] = sprintf('<meta property="twitter:description" content="%s" />',
            get_the_excerpt($post_id)
        );

        $meta[] = sprintf('<meta property="twitter:image" content="%s" />',
            esc_url($cover[0])
        );

        $meta[] = sprintf('<meta property="vk:image" content="%s" />',
            esc_url($cover[0])
        );

         return $meta;
    }


    private function archive_meta($meta = []) {
        $cover = get_template_directory_uri() . '/assets/images/poster-default.png';

        $meta[] = sprintf('<meta name="description" content="%s">',
            get_bloginfo('description')
        );

        $meta[] = '<meta property="og:type" content="website" />';

        $meta[] = sprintf('<meta property="og:url" content="%s" />',
            esc_url(home_url('/'))
        );

        $meta[] = sprintf('<meta property="og:title" content="%s" />',
            get_bloginfo('name')
        );

        $meta[] = sprintf('<meta property="og:description" content="%s" />',
            get_bloginfo('description')
        );

        $meta[] = sprintf('<meta property="og:image" content="%s" />',
            esc_url($cover)
        );

        $meta[] = '<meta property="og:image:width" content="1200" />';

        $meta[] = '<meta property="og:image:height" content="800" />';

        $meta[] = sprintf('<meta property="twitter:title" content="%s" />',
            get_bloginfo('name')
        );

        $meta[] = sprintf('<meta property="twitter:description" content="%s" />',
            get_bloginfo('description')
        );

        $meta[] = sprintf('<meta property="twitter:image" content="%s" />',
            esc_url($cover)
        );

        $meta[] = sprintf('<meta property="vk:image" content="%s" />',
            esc_url($cover)
        );

        return $meta;
    }


    private function common_meta($meta = []) {
        $meta[] = '<meta property="fb:app_id" content="1281081571902073" />';

        $meta[] = '<meta name="twitter:card" content="summary_large_image" />';

        $meta[] = '<meta name="twitter:site" content="@knife_media" />';

        $meta[] = '<meta name="twitter:creator" content="@knife_media" />';

        $meta[] = sprintf(
            '<meta property="og:site_name" content="%s" />', get_bloginfo('name')
        );

         $meta[] = sprintf(
            '<meta property="og:locale" content="%s" />', get_locale()
        );

        return $meta;
    }


    private function get_cover($post_id) {
        $social = get_post_meta($post_id, '_social-image', true);

        if(empty($social)) {
            return wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'outer');
        }

        return [$social, 1024, 512];
    }
}
