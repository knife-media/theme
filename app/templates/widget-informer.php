<?php
/**
 * Informer widget template
 *
 * Informer is an important single post with feature meta
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.7
 */
?>

<a class="widget-informer__inner" <?php echo implode(' ', $options); ?>>
    <div class="widget-informer__content">
        <?php
            printf(
                '<p class="widget-informer__content-title">%s</p>',
                esc_html($instance['title'])
            );

            if(!empty($instance['emoji'])) {
                printf(
                    '<span class="widget-informer__content-emoji">%s</span>',
                    wp_encode_emoji($instance['emoji'])
                );
            }
        ?>
        <span class="icon icon--right"></span>
    </div>
</a>
