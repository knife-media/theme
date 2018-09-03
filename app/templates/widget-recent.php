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
            '<div class="widget-recent__content-meta meta">', '</div>',
            ['time', 'tag'], true
        );

        printf(
            '<a class="widget-recent__content-link" href="%1$s">%2$s</a>',
            get_permalink(),
            get_the_title()
        );
    ?>
</div>
