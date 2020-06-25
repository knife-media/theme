<?php
/**
 * Snippets to foreign plugins
 *
 * Filters and actions same as functions.php file for plugins only
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.13
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
