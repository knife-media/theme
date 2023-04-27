<?php
/**
 * News widget template
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.17
 */
?>

<div class="widget-news__wrapper">
    <div class="widget-news__content">
        <?php
        printf(
            '<a class="widget-news__content-link" href="%1$s">%2$s</a>',
            esc_url( get_permalink() ),
            wp_kses(
                get_the_title(),
                array(
                    'em' => array(),
                )
            )
        );

        the_info(
            '<div class="widget-news__content-info info">',
            '</div>',
            array( 'time', 'date', 'tags' )
        );
        ?>
    </div>
</div>
