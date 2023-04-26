<?php
/**
 * Google Custom Search results template
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.17
 */

get_header(); ?>

<div class="search">
    <?php
        printf(
            '<div class="gcse-search" data-linkTarget="_blank" data-mobileLayout="disabled" data-placeholder="%s"></div>',
            esc_attr__( 'Поиск по сайту', 'knife-theme' )
        );
        ?>
</div>

<?php
get_footer();
