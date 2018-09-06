<?php
/**
 * Club widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-club__inner">
    <div class="widget-club__content">
        <?php
            the_info(
                '<div class="widget-club__content-meta meta">', '</div>',
                ['author', 'date']
            );

            printf(
                '<a class="widget-club__content-link" href="%2$s">%1$s</a>',
                get_the_title(),
                esc_url(get_permalink())
            );
        ?>
    </div>
</div>
