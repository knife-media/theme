<?php
/**
 * Club widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<div class="widget-club__wrapper">
    <?php
        printf('<a class="widget-club__head head" href="%2$s">%1$s</a>',
            esc_html($instance['title']),
            esc_url(get_post_type_archive_link('club'))
        );
    ?>

    <?php while($query->have_posts()) : $query->the_post(); ?>
        <div class="widget-club__inner">
            <div class="widget-club__content">
                <?php
                    the_info(
                        '<div class="widget-club__content-info info">', '</div>',
                        ['author']
                    );

                    printf(
                        '<a class="widget-club__content-link" href="%2$s">%1$s</a>',
                        get_the_title(),
                        esc_url(get_permalink())
                    );
                ?>
            </div>
        </div>
    <?php endwhile; ?>

    <?php
        if(!empty($instance['link'])) {
            printf('<div class="widget-club__more"><a class="widget-club__more-button button" href="%2$s">%1$s</a></div>',
                __('Написать в клуб', 'knife-theme'),
                esc_url($instance['link'])
            );
        }
    ?>

    <?php wp_reset_query(); ?>
</div>
