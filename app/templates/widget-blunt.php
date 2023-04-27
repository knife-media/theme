<?php
/**
 * Blunt widget template
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.17
 */
?>

<div class="widget-blunt__wrapper">
    <?php
    while ( $query->have_posts() ) :
        $query->the_post();
        ?>
        <div class="widget-blunt__inner">
            <?php
            printf(
                '<a class="widget-blunt__link" href="%1$s">%2$s</a>',
                esc_url( get_permalink() ),
                wp_kses(
                    get_the_title(),
                    array(
                        'em' => array(),
                    )
                )
            );

            the_info( '<div class="widget-blunt__pixel">', '</div>', array( 'pixel' ) );
            ?>
        </div>
    <?php endwhile; ?>

    <?php wp_reset_postdata(); ?>
</div>
