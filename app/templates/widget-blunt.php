<?php
/**
 * Blunt widget template
 *
 * @package knife-theme
 * @since 1.7
 */
?>

<div class="widget-blunt__inner">
    <?php
        printf(
            '<a class="widget-blunt__link" href="%1$s">%2$s</a>',
            esc_html(get_permalink()),
            get_the_title()
        );
    ?>
</div>
