<?php
/**
 * Content filters
 *
 * @package knife-theme
 * @since 1.10
 */

if (!defined('WPINC')) {
    die;
}

class Knife_Content_Filters {
    /**
     * Use this method instead of constructor to avoid multiple hook setting
     */
    public static function load_module() {
        // Remove extra &nbsp; from content on save
        add_filter('content_save_pre', [__CLASS__, 'remove_nbsp']);

        // Replace t.me links with custom telegram reditrecter
        add_filter('content_save_pre', [__CLASS__, 'replace_telegram_links']);

        // Remove extra p from shortcodes
        add_filter('the_content', [__CLASS__, 'fix_shortcodes']);

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
     * Replace t.me links with custom tgram.link host
     */
    public static function replace_telegram_links($content) {
        $content = preg_replace('~href="https://t.me/~is', 'href="https://tgram.link/', wp_unslash($content));

        return wp_slash($content);
    }


    /**
     * Remove extra p from shortcodes
     */
    public static function fix_shortcodes($content) {
        $array = [
            '<p>[' => '[',
            ']</p>' => ']',
            ']<br />' => ']'
        ];

        return strtr($content, $array);
    }


    /**
     * Remove extra &nbsp; from content on save
     */
    public static function remove_nbsp($content) {
        return preg_replace('~((&nbsp;|\s)+$)~is', '', $content);
    }
}


/**
 * Load current module environment
 */
Knife_Content_Filters::load_module();

