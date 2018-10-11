<?php
/**
 * Select widget template
 *
 * @package knife-theme
 * @since 1.5
 */
?>

<div class="widget-select__inner">
    <?php
        printf(
            '<a class="widget-select__link" href="%2$s">%1$s</a>',
            get_the_title(),
            esc_url(get_permalink())
        );
    ?>
</div>
