<?php
/**
 * Informer widget template
 *
 * Informer is an important single post with feature meta
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.5
 */
?>

<a class="widget-informer__inner" <?php echo implode(' ', $options); ?>>
    <div class="widget-informer__content">
        <?php
            printf(
                '<p class="widget-informer__content-title">%s</p>',
                esc_html($instance['title'])
            );

            if(!empty($instance['sticker'])) {
                printf(
                    '<img class="widget-informer__content-sticker" src="%1$s" alt="%2$s">',
                    esc_url($instance['sticker']), sanitize_text_field($instance['title'])
                );
            }
        ?>
        <span class="icon icon--right"></span>
    </div>
</a>
