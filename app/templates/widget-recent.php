<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.17
 */
?>

<div class="widget-recent__wrapper">
    <?php
    printf(
        '<a class="widget-recent__head head" href="%s">%s</a>',
        esc_url( get_category_link( get_category_by_slug( $this->news_name ) ) ),
        esc_html( $instance['title'] )
    );
    ?>

    <?php
    while ( $query->have_posts() ) :
        $query->the_post();
        ?>
        <div class="widget-recent__content">
            <?php
            the_info(
                '<div class="widget-recent__content-info info">',
                '</div>',
                array( 'time', 'tag' )
            );

            printf(
                '<a class="widget-recent__content-link" href="%s">%s</a>',
                esc_url( get_permalink() ),
                wp_kses(
                    get_the_title(),
                    array(
                        'em' => array(),
                    )
                )
            );

            the_info( '<div class="widget-recent__content-pixel">', '</div>', array( 'pixel' ) );
            ?>
        </div>
    <?php endwhile; ?>

    <?php
        printf(
            '<a class="widget-recent__more button" href="%s">%s</a>',
            esc_url( get_category_link( get_category_by_slug( $this->news_name ) ) ),
            esc_html__( 'Все новости', 'knife-theme' )
        );
        ?>

    <?php wp_reset_postdata(); ?>
</div>
