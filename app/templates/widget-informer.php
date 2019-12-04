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
            if(!empty($instance['remark'])) :
                printf(
                    '<p class="widget-informer__content-remark">%s</p>',
                    esc_html($instance['remark'])
                );
            endif;

            printf(
                '<p class="widget-informer__content-title">%s</p>',
                esc_html($instance['title'])
            );
        ?>

        <span class="icon icon--right"></span>
    </div>
</a>
