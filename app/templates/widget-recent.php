<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-recent__content">
    <?php
        the_info(
            '<div class="widget-recent__content-info info">', '</div>',
            ['time', 'tag']
        );

        printf(
            '<a class="widget-recent__content-link" href="%1$s">%2$s</a>',
            esc_url(get_permalink()),
            get_the_title()
        );
    ?>
</div>
