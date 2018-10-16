<?php
/**
 * Select archive loop partial template
 *
 * @package knife-theme
 * @since 1.5
 */
?>

<div class="select">
    <?php
        printf(
            '<a class="select__link" href="%2$s">%1$s</a>',
            get_the_title(),
            esc_url(get_permalink())
        );
    ?>
</div>
