<?php
/**
 * Content filters
 *
 * @package knife-theme
 * @since 1.10
 * @version 1.17
 */

if ( ! defined( 'WPINC' ) ) {
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
        // Prevent to create WordPress posts and pages with specific slugs
        add_filter( 'wp_unique_post_slug_is_bad_flat_slug', array( __CLASS__, 'prevent_theme_slugs' ), 10, 2 );
        add_filter( 'wp_unique_post_slug_is_bad_hierarchical_slug', array( __CLASS__, 'prevent_theme_slugs' ), 10, 2 );

        // Remove video and audio shortcodes
        add_action( 'init', array( __CLASS__, 'remove_media_shortcodes' ) );

        // Add custom post styles to admin page
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'add_post_styles' ) );

        // Add target blank to all links
        add_filter( 'content_save_pre', array( __CLASS__, 'add_links_target' ) );

        // Remove extra &nbsp; from content on save
        add_filter( 'content_save_pre', array( __CLASS__, 'remove_nbsp' ) );

        // Wrap pure iframes
        add_filter( 'content_save_pre', array( __CLASS__, 'wrap_iframe' ) );

        // Remove all spans from content
        add_filter( 'content_save_pre', array( __CLASS__, 'remove_span' ) );

        // Remove linked space
        add_filter( 'content_save_pre', array( __CLASS__, 'remove_linked_space' ) );

        // Filter scripts for unprivileged users
        add_filter( 'content_save_pre', array( __CLASS__, 'filter_scripts' ) );

        // Remove inappropriate links from content
        add_filter( 'content_save_pre', array( __CLASS__, 'remove_content_links' ) );

        // Replace card comment with entry-content
        add_filter( 'the_content', array( __CLASS__, 'show_cards' ), 5 );

        // Replace script figure
        add_filter( 'the_content', array( __CLASS__, 'replace_scripts' ), 12 );

        // Replace video links with video tags
        add_filter( 'wp_embed_handler_video', array( __CLASS__, 'replace_video_link' ), 10, 3 );

        // Replace audio links with audio tags
        add_filter( 'wp_embed_handler_audio', array( __CLASS__, 'replace_audio_link' ), 10, 3 );

        // Replace video and audio shortcodes on media send to editor
        add_filter( 'media_send_to_editor', array( __CLASS__, 'replace_media_shortcode' ), 10, 3 );

        // Disable embeds
        add_action(
            'wp_enqueue_scripts',
            function() {
                wp_deregister_script( 'wp-embed' );
            }
        );

        // We don't use gutenberg for now so it would be better to remove useless styles
        add_action(
            'wp_print_styles',
            function() {
                wp_dequeue_style( 'wp-block-library' );
                wp_dequeue_style( 'global-styles' );
                wp_dequeue_style( 'wp-block-library-theme' );
            },
            11
        );

        // Remove default media players
        add_action(
            'wp_enqueue_scripts',
            function() {
                wp_deregister_script( 'wp-mediaelement' );
                wp_deregister_style( 'wp-mediaelement' );
            }
        );
    }

    /**
     * Remove inappropriate links from content
     * I really sorry for this action
     *
     * @since 1.16
     */
    public static function remove_content_links( $content ) {
        $content = preg_replace( '#<a[^>]+href="https://meduza\.io(?:/|")[^>]*>([^<]+)</a>#is', '$1', wp_unslash( $content ) );

        return wp_slash( $content );
    }

    /**
     * Remove media shortcodes
     *
     * @since 1.16
     */
    public static function remove_media_shortcodes() {
        remove_shortcode( 'video' );
        remove_shortcode( 'audio' );
    }

    /**
     * Enqueue assets to admin post screen only
     */
    public static function add_post_styles( $hook ) {
        global $post;

        if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
            return;
        }

        $version = wp_get_theme()->get( 'Version' );
        $include = get_template_directory_uri() . '/core/include';

        // insert admin styles
        wp_enqueue_style( 'knife-post-styles', $include . '/styles/post-styles.css', array(), $version );
    }

    /**
     * Replace content script figure
     *
     * @since 1.11
     */
    public static function replace_scripts( $content ) {
        if ( strpos( $content, 'figure--script' ) !== false ) {
            // Decode figure scrilt data
            $content = preg_replace_callback(
                self::$script_regex,
                function( $matches ) {
                    $figure = $matches[0];

                    if ( isset( $matches[1] ) ) {
                        $script = html_entity_decode( $matches[1], ENT_QUOTES );

                        // Create new figure
                        $figure = '<figure class="figure figure--script">' . $script . '</figure>';
                    }

                    return $figure;
                },
                $content
            );
        }

        return $content;
    }

    /**
     * Prevent to create WordPress posts and pages with specific slugs
     */
    public static function prevent_theme_slugs( $bad_slug, $slug ) {
        $skip = array( 'external', 'requests', 'frame', 'feature', 'analytics' );

        if ( in_array( $slug, $skip, true ) ) {
            return true;
        }

        return $bad_slug;
    }

    /**
     * Replace card comment with entry-content
     */
    public static function show_cards( $content ) {
        return str_replace( '<!--card-->', '</div><div class="entry-content">', force_balance_tags( $content ) );
    }

    /**
     * Add target attr to links
     *
     * @since 1.14
     */
    public static function add_links_target( $content ) {
        $content = wp_unslash( $content );

        // Use custon links_add_target function for now
        $content = preg_replace_callback( '!<a((\s[^>]*)?)>!i', array( __CLASS__, 'replace_links_target' ), $content );

        return wp_slash( $content );
    }

    /**
     * Filter content unsafed scripts
     *
     * @since 1.11
     */
    public static function filter_scripts( $content ) {
        if ( current_user_can( 'unfiltered_html' ) ) {
            return $content;
        }

        $content = preg_replace( self::$script_regex, '', wp_unslash( $content ) );

        return wp_slash( $content );
    }

    /**
     * Wrap pure iframes with figure tag
     */
    public static function wrap_iframe( $content ) {
        $content = wp_unslash( $content );

        if ( preg_match_all( '~(<iframe.+?/iframe>)\s*(?!</figure)~is', $content, $matches ) ) {
            // Loop through all pure frames
            foreach ( $matches[1] as $iframe ) {
                $content = str_replace( $iframe, '<figure class="figure figure--embed">' . $iframe . '</figure>', $content );
            }
        }

        return wp_slash( $content );
    }

    /**
     * Remove extra &nbsp; from content on save
     */
    public static function remove_nbsp( $content ) {
        $content = wp_unslash( $content );

        // Remove all trailing spaces
        $content = preg_replace( '~((&nbsp;| |\s)+$)~is', '', $content );

        return wp_slash( $content );
    }

    /**
     * Remove span from content
     *
     * Skip reference spans with data-body or id
     */
    public static function remove_span( $content ) {
        $content = wp_unslash( $content );

        // Find and remove empty spans
        $content = preg_replace( '~<span(?!\s+(data-body|id)).*?>(.*?)</span>~is', '$1', $content );

        return wp_slash( $content );
    }

    /**
     * Remove linked spaces
     *
     * @link https://github.com/knife-media/theme/issues/177
     */
    public static function remove_linked_space( $content ) {
        $content = wp_unslash( $content );

        // Find and remove spaces inside links
        $content = preg_replace( '~(<a[^>]+>)(\s+)(.*?</a>)~is', '$2$1$3', $content );

        return wp_unslash( $content );
    }

    /**
     * Replace video links with tags
     *
     * @since 1.16
     */
    public static function replace_video_link( $video, $attr, $url ) {
        $html = sprintf(
            '<figure class="figure figure--embed"><video src="%s" controls></video></figure>',
            esc_url( $url )
        );

        return $html;
    }

    /**
     * Replace audio links with tags
     *
     * @since 1.16
     */
    public static function replace_audio_link( $video, $attr, $url ) {
        $html = sprintf(
            '<figure class="figure figure--embed"><audio src="%s" controls></audio></figure>',
            esc_url( $url )
        );

        return $html;
    }

    /**
     * Replace audio and video shortcodes on media send to editor
     *
     * @since 1.16
     */
    public static function replace_media_shortcode( $html, $send_id, $attachment ) {
        if ( wp_attachment_is( 'video', $send_id ) ) {
            $html = sprintf(
                '<figure class="figure figure--embed"><video src="%s" controls></video></figure>',
                esc_url( $attachment['url'] )
            );
        }

        if ( wp_attachment_is( 'audio', $send_id ) ) {
            $html = sprintf(
                '<figure class="figure figure--embed"><audio src="%s" controls></audio></figure>',
                esc_url( $attachment['url'] )
            );
        }

        return $html;
    }

    /**
     * Replace links target only for external links
     */
    private static function replace_links_target( $matches ) {
        if ( preg_match( '~href="#~i', $matches[1] ) ) {
            return $matches[0];
        }

        if ( preg_match( '~data-internal~i', $matches[1] ) ) {
            return $matches[0];
        }

        // Remove target from link to add new one below
        $link = preg_replace( '~( target=([\'"])(.*?)\2)~i', '', $matches[1] );
        return '<a' . $link . ' target="_blank">';
    }
}

/**
 * Load current module environment
 */
Knife_Content_Filters::load_module();
