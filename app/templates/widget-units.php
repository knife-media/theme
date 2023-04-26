<?php
/**
 * Unit widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */
?>

<div class="unit unit--<?php echo esc_attr( $size ); ?>">
    <div class="unit__inner">
        <?php
        the_info(
            '<div class="unit__head">',
            '</div>',
            array( 'head' )
        );
        ?>

        <div class="unit__image">
            <?php
            the_post_thumbnail(
                $size,
                array(
                    'class'   => 'unit__image-thumbnail',
                    'loading' => 'lazy',
                )
            );
            ?>
        </div>

        <div class="unit__content">
            <?php
            printf(
                '<a class="unit__content-link" href="%1$s">%2$s</a>',
                esc_url( get_permalink() ),
                wp_kses(
                    get_the_title(),
                    array(
                        'em' => array(),
                    )
                )
            );

            the_info(
                '<div class="unit__content-info">',
                '</div>',
                array( 'author', 'date', 'best' )
            );

            the_info( '<div class="unit__content-pixel">', '</div>', array( 'pixel' ) );
            ?>
        </div>
    </div>
</div>
