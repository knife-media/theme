<?php
/**
* Add custom site header meta and footer description
*
* @package knife-theme
* @since 1.5
*/


if (!defined('WPINC')) {
    die;
}


class Knife_Site_Meta {
    /**
     * Option to store footer description
     *
     * @access  private
     * @var     string
     */
    private static $footer_description = 'footer-description';


    /**
     * Init function instead of constructor
     */
    public static function load_module() {
        add_action('wp_head', [__CLASS__, 'add_meta'], 5);
        add_action('wp_head', [__CLASS__, 'add_icon'], 4);
        add_action('wp_head', [__CLASS__, 'add_mediator'], 6);

        // Add custom theme lang attributes
        add_filter('language_attributes', [__CLASS__, 'add_xmlns']);

        // Add footer description field to customizer
        add_action('customize_register', [__CLASS__, 'add_customize_setting']);
    }


    /**
     * Footer description option
     */
    public static function add_customize_setting($wp_customize) {
        $wp_customize->add_setting(self::$footer_description);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize,
            self::$footer_description, [
                 'label' => __('Описание в подвале', 'knife-theme'),
                 'section' => 'title_tagline',
                 'type' => 'textarea',
                 'priority' => 10
             ]
        ));
    }


    public static function add_icon() {
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


    public static function add_xmlns($output) {
        return 'prefix="og: http://ogp.me/ns#" ' . $output;
    }


    /**
     * Add mediator tags to singular templates
     *
     * @link https://mediator.media/ru/install/
     */
    public static function add_mediator() {
        if(!is_singular()) {
            return;
        }

        global $post;

        $meta = [];

        // Add post author to meta
        if(function_exists('get_coauthors')) {
            foreach(get_coauthors() as $author) {
                $meta[] = sprintf('<meta name="mediator_author" content="%s" />', $author->display_name);
            }
        }

        // Add post terms to meta
        foreach(wp_get_post_tags($post->ID) as $term) {
            $meta[] = sprintf('<meta name="mediator_theme" content="%s" />', $term->name);
        }

        // Add published time to meta
        $meta[] = sprintf('<meta name="mediator_published_time" content="%s" />', substr_replace(get_the_time('c'), '', -3, 1));

        // Print mediator custom meta
        foreach($meta as $tag) {
            echo "$tag\n";
        }
    }


    public static function add_meta() {
        $meta = self::common_meta();

        $meta = (is_singular() && !is_front_page()) ? self::single_meta($meta) : self::archive_meta($meta);

        foreach($meta as $tag) {
            echo "$tag\n";
        }
    }


    private static function single_meta($meta = []) {
        $post_id = get_queried_object_id();

        $cover = self::get_cover($post_id);

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


    private static function archive_meta($meta = []) {
        $cover = get_template_directory_uri() . '/assets/images/poster-feature.png';

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


    private static function common_meta($meta = []) {
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


    private static function get_cover($post_id) {
        $social = get_post_meta($post_id, '_social-image', true);

        if(empty($social)) {
            return wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'outer');
        }

        return [$social, 1024, 512];
    }
}


/**
 * Load current module environment
 */
Knife_Site_Meta::load_module();
