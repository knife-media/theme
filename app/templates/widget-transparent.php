<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.15
 */
?>

<div class="widget-transparent__wrapper">
    <?php
        if(!empty($instance['title']) && !empty($instance['link'])) :
            printf(
                '<div class="widget-transparent__head"><a class="head" href="%2$s">%1$s</a></div>',
                esc_html($instance['title']), esc_url($instance['link'])
            );
        endif;
    ?>

    <?php while($query->have_posts()) : $query->the_post(); ?>
        <div class="widget-transparent__inner">
            <?php
                $emoji = $this->get_emoji(get_the_ID());

                if(!empty($emoji)) :
                    printf(
                        '<span class="widget-transparent__emoji">%s</span>',
                        wp_encode_emoji($emoji)
                    );
                endif;
            ?>

            <div class="widget-transparent__content">
                <?php
                    the_info(
                        '<div class="widget-transparent__content-info info">', '</div>',
                        ['author', 'date']
                    );

                    printf(
                        '<a class="widget-transparent__content-link" href="%2$s">%1$s</a>',
                        get_the_title(), esc_url(get_permalink())
                    );

                    the_info('<div class="widget-transparent__content-pixel">', '</div>', ['pixel']);
                ?>
            </div>
        </div>
    <?php endwhile; ?>

    <?php wp_reset_query(); ?>
</div>
