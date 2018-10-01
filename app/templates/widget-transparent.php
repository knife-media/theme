<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-transparent__inner">
    <?php
        if(!empty($image)) {
            printf(
                '<img class="widget-transparent__sticker" src="%1$s" alt="%2$s">',
                esc_url($image), sanitize_text_field(get_the_title())
            );
        }
    ?>

    <div class="widget-transparent__content">
        <?php
            the_info(
                '<div class="widget-transparent__content-info">', '</div>',
                ['author', 'date']
            );

            printf(
                '<a class="widget-transparent__content-link" href="%2$s">%1$s</a>',
                get_the_title(), esc_url(get_permalink())
            );
        ?>
    </div>
</div>
