<?php
/**
 * special loop template
 *
 * @package knife-theme
 * @since 1.15
 */
?>

<div class="unit">
    <?php
        printf(
            '<p class="unit__cover">%s</p>',
            get_post_meta(get_the_ID(), 'post-emoji', true)
        );
    ?>

    <div class="unit__content">
        <?php
            printf(
                '<a class="unit__content-link" href="%1$s">%2$s</a>',
                esc_url(get_permalink()),
                get_post_meta(get_the_ID(), 'post-info', true)
            );
        ?>
    </div>
</div>
