<?php
/**
 * Google Custom Search results template
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<div class="search">
    <?php
        printf(
            '<div class="gcse-search" data-linkTarget="_blank" data-mobileLayout="disabled" data-placeholder="%s"></div>',
            __('Поиск по сайту', 'knife-theme')
        );
    ?>
</div>

<?php get_footer();
