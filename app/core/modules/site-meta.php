<?php
/**
 * Add custom site header meta and footer description
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.9
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
        add_action('wp_head', [__CLASS__, 'add_header_icons'], 4);
        add_action('wp_head', [__CLASS__, 'add_seo_tags'], 4);

        add_action('wp_head', [__CLASS__, 'add_og_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_twitter_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_facebook_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_vk_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_yandex_meta'], 6);

        add_action('wp_head', [__CLASS__, 'add_mediator_meta'], 7);

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

        $wp_customize->add_section('knife_footer', [
            'title' => __('Подвал сайта','knife-theme'),
            'priority' => 160,
        ]);

        $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize,
            self::$footer_description, [
                 'label' => __('Описание в подвале', 'knife-theme'),
                 'section' => 'knife_footer',
                 'code_type' => 'text/html',
                 'priority' => 10
             ]
        ));
    }


    /**
     * Add og xmlns
     */
    public static function add_xmlns($output) {
        return 'prefix="og: http://ogp.me/ns#" ' . $output;
    }


    /**
     * Add header static icons
     */
    public static function add_header_icons() {
        $meta = [];

        // Get assets path
        $path = get_template_directory_uri() . '/assets/images';

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="32x32" href="%s" />',
            esc_url($path . '/icon-32.png')
        );

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="192x192" href="%s" />',
            esc_url($path . '/icon-192.png')
        );

        $meta[] = sprintf(
            '<link rel="apple-touch-icon" sizes="180x180" href="%s" />',
            esc_url($path . '/icon-180.png')
        );

        return self::print_tags($meta);
    }


    /**
     * Add mediator tags to singular templates
     *
     * @link https://mediator.media/ru/install/
     */
    public static function add_mediator_meta() {
        $meta = [];

        if(is_singular() && !is_page()) {
            if(function_exists('get_coauthors')) {
                foreach(get_coauthors() as $author) {
                    $meta[] = sprintf(
                        '<meta name="mediator_author" content="%s" />',
                        esc_attr($author->display_name)
                    );
                }
            }

            foreach(wp_get_post_tags(get_queried_object_id()) as $term) {
                $meta[] = sprintf(
                    '<meta name="mediator_theme" content="%s" />',
                    esc_attr($term->name)
                );
            }

            $meta[] = sprintf(
                '<meta name="mediator_published_time" content="%s" />',
                substr_replace(get_the_time('c'), '', -3, 1)
            );
        }

        return self::print_tags($meta);
    }


    /**
     * Add seo tags
     */
    public static function add_seo_tags() {
        $meta = [];

        // Get description
        $description = self::get_description();

        $meta[] = sprintf(
            '<meta name="description" content="%s">',
            esc_attr($description)
        );

        return self::print_tags($meta);
    }


    /**
     * Add yandex browser meta
     *
     * @link https://tech.yandex.ru/browser/tableau/doc/dg/concepts/about-docpage/
     */
    public static function add_yandex_meta() {
        $meta = [];

        // Get assets path
        $path = get_template_directory_uri() . '/assets/images';

        $meta[] = sprintf(
            '<meta name="yandex-tableau-widget" content="logo=%s, color=#002349">',
            esc_url($path . '/logo-feature.png')
        );

        return self::print_tags($meta);
    }


    /**
     * Add og tags
     *
     * @link https://developers.facebook.com/docs/sharing/webmasters
     */
    public static function add_og_tags() {
        $meta = [];

        // Get social image array
        $social_image = self::get_social_image();

        // Get description
        $description = self::get_description();

        $meta[] = sprintf(
            '<meta property="og:site_name" content="%s">',
            esc_attr(get_bloginfo('name'))
        );

        $meta[] = sprintf(
             '<meta property="og:locale" content="%s">',
             esc_attr(get_locale())
        );

        $meta[] = sprintf(
            '<meta property="og:description" content="%s">',
            esc_attr($description)
        );

        $meta[] = sprintf(
            '<meta property="og:image" content="%s">',
            esc_attr($social_image[0])
        );

        $meta[] = sprintf(
            '<meta property="og:image:width" content="%s">',
            esc_attr($social_image[1])
        );

        $meta[] = sprintf(
            '<meta property="og:image:height" content="%s">',
            esc_attr($social_image[2])
        );

        if(is_post_type_archive()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_post_type_archive_link(get_post_type()))
            );
        }

        if(is_tax() || is_category() || is_tag()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_term_link(get_queried_object()->term_id))
            );
        }

        if(is_front_page()) {
            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(home_url('/'))
            );

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(get_bloginfo('title'))
            );
        }

        if(is_singular() && !is_front_page()) {
            $object_id = get_queried_object_id();

            array_push($meta, '<meta property="og:type" content="article">');

            $meta[] = sprintf(
                '<meta property="og:url" content="%s">',
                esc_url(get_permalink($object_id))
            );

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(strip_tags(get_the_title($object_id)))
            );
        }

        if(is_archive()) {
            $object_type = get_queried_object();

            $meta[] = sprintf(
                '<meta property="og:title" content="%s">',
                esc_attr(wp_get_document_title())
            );
        }

        return self::print_tags($meta);
    }


    /**
     * Add twitter tags
     *
     * Note: we shouldn't duplicate og tags
     *
     * @link https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started.html
     */
    public static function add_twitter_tags() {
        $meta = [
            '<meta name="twitter:card" content="summary">',
            '<meta name="twitter:site" content="@knife_media">'
        ];

        return self::print_tags($meta);
    }


    /**
     * Add facebook additional tag
     */
    public static function add_facebook_tags() {
        $meta = [
            '<meta property="fb:app_id" content="1281081571902073">',
            '<meta property="fb:page_id" content="518169241541755">'
        ];

        return self::print_tags($meta);
    }


    /**
     * Add vk image
     */
    public static function add_vk_tags() {
        $meta = [];

        // Get social image
        $social_image = self::get_social_image();

        $meta[] = sprintf(
            '<meta property="vk:image" content="%s">',
            esc_attr($social_image[0])
        );

        return self::print_tags($meta);
    }


    /**
     * Get description
     */
    private static function get_description() {
        if(is_singular() && !is_front_page()) {
            $object_id = get_queried_object_id();

            if(has_excerpt($object_id)) {
                return strip_tags(get_the_excerpt($object_id));
            }
        }

        if(is_archive()) {
            $object_type = get_queried_object();

            if(!empty($object_type->description)) {
                return $object_type->description;
            }

            if(!empty($object_type->name)) {
                $description = __('Журнал Нож – архив статей по теме ', 'knife-theme') . strip_tags($object_type->name);

                return trim($description);
            }
        }

        return get_bloginfo('description');
    }


    /**
     * Get social image cover
     */
    private static function get_social_image() {
        if(is_singular()) {
            $object_id = get_queried_object_id();

            // Custom social image storing via social-image plugin in post meta
            $social_image = get_post_meta($object_id, '_social-image', true);

            if(empty($social_image) && has_post_thumbnail()) {
                return wp_get_attachment_image_src(get_post_thumbnail_id($object_id), 'outer');
            }

            $options = get_post_meta($object_id, '_social-image-options', true);

            // Set size using options
            $options = wp_parse_args($options, [
                'width' => 1200,
                'height' => 630
            ]);

            return array($social_image, $options['width'], $options['height']);
        }

        $social_image = get_template_directory_uri() . '/assets/images/poster-feature.png';

        return array($social_image, 1200, 630);
    }


    /**
     * Print tags if not empty array
     */
    private static function print_tags($meta) {
        foreach($meta as $tag) {
            echo $tag . PHP_EOL;
        }
    }
}


/**
 * Load current module environment
 */
Knife_Site_Meta::load_module();
