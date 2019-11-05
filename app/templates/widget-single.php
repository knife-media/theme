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
        if($internal) :
            the_info(
                '<div class="widget-single__head info">', '</div>',
                ['head']
            );
        endif;
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

            if(empty($instance['button']) && $internal) :
                the_info(
                    '<div class="widget-single__content-info info">', '</div>',
                    ['author', 'label']
                );
            else :
                if(!empty($instance['button'])) :
                    printf(
                        '<button class="widget-single__content-button">%s</button>',
                        esc_html($instance['button'])
                    );
                endif;
            endif;
        ?>
    </div>

    <?php
        if(!empty($instance['pixel'])) {
            echo $instance['pixel'];
        }
    ?>
</div>
