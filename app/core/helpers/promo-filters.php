<?php
/**
 * Promo filters
 *
 * Filters and actions using for upgrading templates only for promo purpose
 * For example: we can hide certain post from rss
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.14
 */


/**
 * Add custom advertisment script for mts-switcher promo
 */
add_action('wp_enqueue_scripts', function() {
    // Get theme version
    $version = wp_get_theme()->get('Version');

    if(defined('WP_DEBUG') && true === WP_DEBUG) {
        $version = date('U');
    }

    // Add scripts
    wp_enqueue_script('knife-mts', '/external/mts-switcher/scripts.min.js', [], $version, true);

    // Add styles
    wp_enqueue_style('knife-mts', '/external/mts-switcher/styles.min.css', [], $version);
});


/**
 * Replace default logo for dobroshrift.ru event
 */
add_action('wp_head', function() {
    $custom_css = "
    .header__logo-image {
        visibility: hidden;
    }

    .header__logo-link {
        display: block;
        width: 5rem;
        height: 2.5rem;

        background-image: url(/wp-content/uploads/2020/10/dobronozh-logo.png);
        background-size: contain;
        background-repeat: no-repeat;
    }";

    $custom_js = "
        document.addEventListener('DOMContentLoaded', function() {
            var logo = document.querySelector('.header__logo-link');
            logo.setAttribute('href', 'https://dobroshrift.ru/');
            logo.setAttribute('target', '_blank');
            logo.setAttribute('rel', 'noopener');
            logo.setAttribute('title', 'Прочитать об акции Доброшрифт');
        });
    ";

    // Add styles
    printf('<style>%s</style>', $custom_css);

    // Add scripts
    printf('<script>%s</script>', $custom_js);
});