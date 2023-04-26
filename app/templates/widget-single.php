<?php
/**
 * Single widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */
?>

<div class="widget-single__wrapper">
    <div class="widget-single__inner">
        <?php
        the_info(
            '<div class="widget-single__head info">',
            '</div>',
            array( 'head' )
        );
        ?>

        <div class="widget-single__image">
            <?php
            echo wp_get_attachment_image(
                $instance['cover'],
                'single',
                false,
                array(
                    'class'   => 'widget-single__image-thumbnail',
                    'loading' => 'lazy',
                )
            );
            ?>
        </div>

        <div class="widget-single__content">
            <?php
            printf(
                '<a class="widget-single__content-title" href="%1$s">%2$s</a>',
                esc_url( $instance['link'] ),
                wp_kses(
                    $instance['title'],
                    array(
                        'em' => array(),
                    )
                )
            );

            the_info(
                '<div class="widget-single__content-info">',
                '</div>',
                array( 'author', 'best' )
            );

            the_info( '<div class="widget-single__content-pixel">', '</div>', array( 'pixel' ) );
            ?>
        </div>
    </div>
</div>
