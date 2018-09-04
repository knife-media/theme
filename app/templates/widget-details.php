<?php
/**
 * Details widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-details__inner">
    <?php
        printf(
            '<a class="widget-details__link" href="%1$s">%2$s</a>',
            esc_html(get_permalink()),
            get_the_title()
        );
    ?>
</div>
