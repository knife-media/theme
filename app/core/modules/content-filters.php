<?php
/**
 * Content filters
 *
 * @package knife-theme
 * @since 1.10
 * @version 1.14
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Content_Filters {
    /**
     * Regular expression to filter script figure
     *
     * @since 1.11
     */
    private static $script_regex = '~<figure[^>]+data-script="([^"]+)"[^>]*>(.*?)</figure>~is';


    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Add custom post styles to admin page
        add_action('admin_enqueue_scripts', [__CLASS__, 'add_post_styles']);

        // Add target blank to all links
        add_filter('content_save_pre', [__CLASS__, 'add_links_target'], 8);

        // Remove extra &nbsp; from content on save
        add_filter('content_save_pre', [__CLASS__, 'remove_nbsp']);

        // Replace t.me links with custom telegram reditrecter
        add_filter('content_save_pre', [__CLASS__, 'replace_telegram_links']);

        // Wrap pure iframes
        add_filter('content_save_pre', [__CLASS__, 'wrap_iframe']);

        // Remove all spans from content
        add_filter('content_save_pre', [__CLASS__, 'remove_span']);

        // Remove linked space
        add_filter('content_save_pre', [__CLASS__, 'remove_linked_space']);

        // Filter scripts for unprivileged users
        add_filter('content_save_pre', [__CLASS__, 'filter_scripts']);

        // Replace card comment with entry-content
        add_filter('the_content', [__CLASS__, 'show_cards'], 5);

        // Replace script figure
        add_filter('the_content', [__CLASS__ , 'replace_scripts'], 12);

        // Disable embeds
        add_action('wp_enqueue_scripts', function() {
            wp_deregister_script('wp-embed');
        });

        // We don't use gutenberg for now so it would be better to remove useless styles
        add_action('wp_print_styles', function() {
            wp_dequeue_style('wp-block-library');
        }, 11);
    }


    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_post_styles($hook) {
        global $post;

        if(!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        $version = wp_get_theme()->get('Version');
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style('knife-post-styles', $include . '/styles/post-styles.css', [], $version);
    }


    /**
     * Add target attr to links
     *
     * @since 1.14
     */
    public static function add_links_target($content) {
        $content = links_add_target($content);

        return $content;
    }


    /**
     * Filter content unsafed scripts
     *
     * @since 1.11
     */
    public static function filter_scripts($content) {
        if(current_user_can('unfiltered_html')) {
            return $content;
        }

        $content = preg_replace(self::$script_regex, '', wp_unslash($content));

        return wp_slash($content);
    }


    /**
     * Replace content script figure
     *
     * @since 1.11
     */
    public static function replace_scripts($content) {
        if(strpos($content, 'figure--script') !== false) {
            // Decode figure scrilt data
            $content = preg_replace_callback(self::$script_regex, function($matches) {
                $figure = $matches[0];

                if(isset($matches[1])) {
                    $script = html_entity_decode($matches[1], ENT_QUOTES);

                    // Create new figure
                    $figure = '<figure class="figure figure--script">' . $script  . '</figure>';
                }

                return $figure;
            }, $content);
        }

        return $content;
    }



    /**
     * Replace t.me links with custom tgram.link host
     */
    public static function replace_telegram_links($content) {
        $content = str_replace('href="https://t.me/', 'href="https://tgram.link/', wp_unslash($content));

        return wp_slash($content);
    }


    /**
     * Replace card comment with entry-content
     */
    public static function show_cards($content) {
        return str_replace('<!--card-->', '</div><div class="entry-content">', force_balance_tags($content));
    }


    /**
     * Wrap pure iframes with figure tag
     */
    public static function wrap_iframe($content) {
        if(preg_match_all('~(<iframe.+?/iframe>)\s*(?!</figure)~is', $content, $matches)) {
            // Loop through all pure frames
            foreach($matches[1] as $iframe) {
                $content = str_replace($iframe, '<figure class="figure figure--embed">' . $iframe . '</figure>', $content);
            }
        }

        return $content;
    }


    /**
     * Remove extra &nbsp; from content on save
     */
    public static function remove_nbsp($content) {
        return preg_replace('~((&nbsp;| |\s)+$)~is', '', $content);
    }


    /**
     * Remove span from content
     *
     * Skip reference spans with data-body
     */
    public static function remove_span($content) {
        return preg_replace('~<span(?!\s+(data-body|id)).*?>(.*?)</span>~is', '$1', $content);
    }


    /**
     * Remove linked spaces
     *
     * @link https://github.com/knife-media/theme/issues/177
     */
    public static function remove_linked_space($content) {
        return preg_replace('~(<a[^>]+>)(\s+)(.*?</a>)~is', '$2$1$3', $content);
    }
}


/**
 * Load current module environment
 */
Knife_Content_Filters::load_module();

