<?php
/**
 * Single widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<div class="widget-single__wrapper">
    <div class="widget-single__inner">
        <?php
            the_info(
                '<div class="widget-single__head info">', '</div>',
                ['head']
            );
        ?>

        <div class="widget-single__image">
            <?php
                echo wp_get_attachment_image(
                    $instance['cover'], 'single', false,
                    ['class' => 'widget-single__image-thumbnail', 'loading' => 'lazy']
                );
            ?>
        </div>

        <div class="widget-single__content">
            <?php
                printf(
                    '<a class="widget-single__content-title" href="%1$s">%2$s</a>',
                    esc_url($instance['link']),
                    wp_kses($instance['title'], [
                        'em' => []
                    ])
                );

                the_info(
                    '<div class="widget-single__content-info info">', '</div>',
                    ['author', 'best']
                );
            ?>
        </div>
    </div>
</div>
