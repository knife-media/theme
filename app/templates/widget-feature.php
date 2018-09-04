<?php
/**
 * Feature widget template
 *
 * Feature is an important single post with feature meta
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<a class="widget-feature__inner" href="<?php echo esc_url($link); ?>" target="_blank" style="background-color: <?php echo $color; ?>">
    <div class="widget-feature__content">
        <?php
            printf(
                '<p class="widget-feature__content-title">%s</p>',
                sanitize_text_field($title)
            );

            if(!empty($sticker)) {
                printf(
                    '<img class="widget-feature__content-sticker" src="%1$s" alt="%2$s">',
                    esc_url($sticker), esc_attr($title)
                );
            }
        ?>
        <span class="icon icon--right"></span>
    </div>
</a>
