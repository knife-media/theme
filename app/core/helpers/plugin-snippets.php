<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.14
 */


/**
 * Set public post preview plugin link ttl
 *
 * @link https://wordpress.org/plugins/public-post-preview
 * @since 1.13
 */
add_filter('ppp_nonce_life', function() {
    return 60 * 60 * 24 * 7; // 7 days
});


/**
 * Temporary replacement for buggy links_add_target function
 *
 * @link https://core.trac.wordpress.org/ticket/51313
 * @since 1.14
 */
function knife_links_add_target($content, $target = '_blank', $tags = array('a')) {
    global $_links_add_target;

    $_links_add_target = $target;
    $tags = implode('|', (array) $tags);

	return preg_replace_callback("!<($tags)((\s[^>]*)?)>!i", '_links_add_target', $content);
}