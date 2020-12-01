<?php
/**
 * Add custom site header meta and footer description
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.14
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
        add_action('wp_head', [__CLASS__, 'add_manifest'], 4);
        add_action('wp_head', [__CLASS__, 'add_seo_tags'], 4);

        add_action('wp_head', [__CLASS__, 'add_og_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_twitter_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_facebook_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_telegram_tags'], 5);
        add_action('wp_head', [__CLASS__, 'add_yandex_meta'], 5);

        // Add JSON-LD microdata
        add_action('wp_head', [__CLASS__, 'add_json_microdata'], 25);

        // Add google tagmanager script
        add_action('wp_head', [__CLASS__, 'add_tagmanager'], 20);

        // Add custom theme lang attributes
        add_filter('language_attributes', [__CLASS__, 'add_xmlns']);

        // Add footer description field to customizer
        add_action('customize_register', [__CLASS__, 'update_customize_settings']);

        // Remove comments feed link
        // For the reason that we don't use comments in this theme we have to remove comments feed link from header
        add_filter('feed_links_show_comments_feed', '__return_false');

        // Include to page current page parameters
        add_action('wp_enqueue_scripts', [__CLASS__, 'inject_parameters'], 12);
    }


    /**
     * Add JSON-LD microdata for singular templates
     *
     * @since 1.11
     */
    public static function add_json_microdata() {
        // Show microdata only for singular
        if(!is_singular()) {
            return;
        }

        // Get singular post id
        $post_id = get_queried_object_id();

        $schema = [
            '@context' => 'http://schema.org',
            '@type' => 'NewsArticle'
        ];

        if(has_category('longreads', $post_id)) {
            $schema['@type'] = 'Article';
        }

        $schema['url'] = get_permalink($post_id);

        // Set unique id with text element in anchor
        $schema['@id'] = $schema['url'] . '#post-' . $post_id;

        // Set post date
        $schema['datePublished'] = get_the_date('c', $post_id);

        // Set post modified date
        $schema['dateModified'] = get_the_modified_date('c', $post_id);

        // Set post title
        $schema['headline'] = wp_strip_all_tags(get_the_title($post_id));

        // Add authors
        if(property_exists('Knife_Authors_Manager', 'meta_authors')) {
            $authors = get_post_meta($post_id, Knife_Authors_Manager::$meta_authors);

            if($authors) {
                $users = get_users([
                    'include' => $authors,
                    'fields' => ['display_name']
                ]);

                foreach($users as $user) {
                    $author = [
                        'type' => 'Person',
                        'name' => $user->display_name
                    ];

                    $schema['author'][] = $author;
                }
            }
        }

        // Get tags
        $tags = get_the_tags($post_id);

        if(!$tags) {
            $tags = [];
        }

        foreach($tags as $tag) {
            $about = [
                'name' => $tag->slug
            ];

            $schema['about'][] = $about;
        }

        // Add text parameter only for posts
        if(get_post_type($post_id) === 'post') {
            $content = get_the_content(null, false, $post_id);

            if(method_exists('Knife_Post_Lead', 'get_lead')) {
                $content = Knife_Post_Lead::get_lead($post_id) . $content;
            }

            $content = preg_replace('~[ \t\r\n]+~', ' ', $content);

            // Strip content tags
            $schema['text'] = wp_strip_all_tags($content);
        }

        printf(
            '<script type="application/ld+json">%s</script>',
            json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
        );
    }


    /**
     * Add tagmanager script to header
     */
    public static function add_tagmanager() {
        if(defined('KNIFE_TAGMANAGER')) {
            $include = get_template_directory() . '/core/include';
            include_once($include . '/templates/tagmanager-script.php');
        }
    }


    /**
     * Footer description option
     */
    public static function update_customize_settings($wp_customize) {
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

        // Remove site icon controls from admin customizer
        $wp_customize->remove_control('site_icon');
    }


    /**
     * Add og xmlns
     */
    public static function add_xmlns($output) {
        return 'prefix="og: http://ogp.me/ns#" ' . $output;
    }


    /**
     * Add manifest and header icons
     *
     * @since 1.11
     */
    public static function add_manifest() {
        $meta = [];

        $meta[] = sprintf(
            '<link rel="manifest" href="%s" crossorigin="use-credentials">',
            esc_url(home_url('/manifest.json'))
        );

        $meta[] = sprintf(
            '<link rel="shortcut icon" href="%s" crossorigin="use-credentials">',
            esc_url(home_url('/favicon.ico'))
        );

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="32x32" href="%s">',
            esc_url(home_url('/icon-32.png'))
        );

        $meta[] = sprintf(
            '<link rel="icon" type="image/png" sizes="192x192" href="%s">',
            esc_url(home_url('/icon-192.png'))
        );

        $meta[] = sprintf(
            '<link rel="apple-touch-icon" sizes="180x180" href="%s">',
            esc_url(home_url('/icon-180.png'))
        );

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

        if(method_exists('Knife_Snippet_Image', 'get_social_image')) {
            // Get social image array
            $social_image = Knife_Snippet_Image::get_social_image();

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
        }

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
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:site" content="@knife_media">'
        ];

        if(method_exists('Knife_Snippet_Image', 'get_social_image')) {
            // Get social image array
            $social_image = Knife_Snippet_Image::get_social_image();

            $meta[] = sprintf(
                '<meta name="twitter:image" content="%s">',
                esc_attr($social_image[0])
            );
        }

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
     * Add telegram meta tag
     */
    public static function add_telegram_tags() {
        $meta = [
            '<meta name="telegram:channel" content="@knifemedia">'
        ];

        return self::print_tags($meta);
    }


    /**
     * Inject current page parameters for external systems
     *
     * @since 1.11
     */
    public static function inject_parameters() {
        $meta = [];

        if(is_singular() && !is_front_page()) {
            $meta = self::get_singular_parameters($meta);
        }

        if(is_archive()) {
            $meta = self::get_archive_parameters($meta);
        }

        if(is_front_page()) {
            $meta['template'] = 'front';
        }

        // Add custom meta parameters
        wp_localize_script('knife-theme', 'knife_meta_parameters', $meta);
    }


    /**
     * Get template format from page template function
     *
     * @since 1.14
     */
    private static function get_template_format() {
        $format = basename(get_page_template_slug(), '.php');

        // Split format parts
        $parts = explode('-', $format);

        if($parts[0] === 'single' && isset($parts[1])) {
            return $parts[1];
        }

        return 'default';
    }


    /**
     * Get singular custom meta parameters
     *
     * @since 1.11
     */
    private static function get_singular_parameters($meta) {
        // Append post id
        $meta['postid'] = get_queried_object_id();

        // Append template
        $meta['template'] = get_post_type();

        // Set page template as format
        $meta['format'] = self::get_template_format();

        if(property_exists('Knife_Adult_Content', 'meta_adult')) {
            $adult = get_post_meta($meta['postid'], Knife_Adult_Content::$meta_adult, true);

            // Add adult content meta
            $meta['adult'] = (int) $adult;
        }

        if(property_exists('Knife_Promo_Manager', 'meta_promo')) {
            $promo = get_post_meta($meta['postid'], Knife_Promo_Manager::$meta_promo, true);

            // Add adult content meta
            $meta['promo'] = (int) $promo;
        }

        if(property_exists('Knife_Authors_Manager', 'meta_authors')) {
            $authors = get_post_meta($meta['postid'], Knife_Authors_Manager::$meta_authors);

            if($authors) {
                $users = get_users([
                    'include' => $authors,
                    'fields' => ['user_nicename']
                ]);

                // Add authors content meta
                $meta['authors'] = implode(':', wp_list_pluck($users, 'user_nicename'));
            }
        }

        if(property_exists('Knife_Authors_Manager', 'meta_editor')) {
            $editor = get_post_meta($meta['postid'], Knife_Authors_Manager::$meta_editor, true);

            if($editor) {
                $meta['editor'] = $editor;
            }
        }

        if(property_exists('Knife_Special_Projects', 'taxonomy')) {
            $terms = get_the_terms($meta['postid'], Knife_Special_Projects::$taxonomy);

            // Get only first term
            if(isset($terms[0]->slug)) {
                $meta['special'] = $terms[0]->slug;
            }
        }

        $category = get_the_category($meta['postid']);

        // Append categories
        if(is_array($category) && count($category) > 0) {
            $meta['category'] = implode(':', wp_list_pluck($category, 'category_nicename'));
        }

        $tags = get_the_tags($meta['postid']);

        // Append tags
        if(is_array($tags) && count($tags) > 0) {
            $meta['tags'] = implode(':', wp_list_pluck($tags, 'slug'));
        }

        return $meta;
    }


    /**
     * Get archive custom meta parameters
     *
     * @since 1.11
     */
    private static function get_archive_parameters($meta) {
        $object = get_queried_object();

        // Append template
        $meta['template'] = 'archive';

        if(empty($object->slug)) {
            return $meta;
        }

        if(property_exists('Knife_Special_Projects', 'taxonomy')) {
            // Check if in special tax
            if(is_tax(Knife_Special_Projects::$taxonomy)) {
                $meta['special'] = $object->slug;
            }
        }

        if(is_category()) {
            $meta['category'] = $object->slug;
        }

        if(is_tag()) {
            $meta['tags'] = $object->slug;
        }

        return $meta;
    }


    /**
     * Get description
     */
    private static function get_description() {
        if(is_singular() && !is_front_page()) {
            $object_id = get_queried_object_id();

            if(has_excerpt($object_id)) {
                return trim(strip_tags(get_the_excerpt($object_id)));
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
