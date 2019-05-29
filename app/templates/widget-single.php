<?php
/**
 * Single widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.9
 */
?>

<div class="widget-single__inner">
    <?php
        the_info(
            '<div class="widget-single__head">', '</div>',
            ['head']
        );
    ?>

    <div class="widget-single__image">
        <?php
            echo wp_get_attachment_image(
                $instance['cover'], 'single', false,
                ['class' => 'widget-single__image-thumbnail']
            );
        ?>
    </div>

    <div class="widget-single__content">
        <?php
            printf(
                '<a class="widget-single__content-title" href="%1$s">%2$s</a>',
                esc_html(get_permalink()),
                get_the_title()
            );

            if(empty($instance['button'])) :
                the_info(
                    '<div class="widget-single__content-info">', '</div>',
                    ['author', 'label']
                );
            else :
                printf(
                    '<button class="widget-single__content-button">%s</button>',
                    esc_html($instance['button'])
                );
            endif;
        ?>
    </div>
</div>
