<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<div class="widget-recent__wrapper">
    <?php
        printf(
            '<a class="widget-recent__head head" href="%2$s">%1$s</a>',
            esc_html($instance['title']),
            esc_url(get_category_link($this->news_id))
        );
    ?>

    <?php while($query->have_posts()) : $query->the_post(); ?>
        <div class="widget-recent__content">
            <?php
                the_info(
                    '<div class="widget-recent__content-info info">', '</div>',
                    ['time', 'tag']
                );

                printf(
                    '<a class="widget-recent__content-link" href="%1$s">%2$s</a>',
                    esc_url(get_permalink()),
                    get_the_title()
                );
            ?>
        </div>
    <?php endwhile; ?>

    <?php
        printf(
            '<a class="widget-recent__more button" href="%2$s">%1$s</a>',
            __('Все новости', 'knife-theme'),
            esc_url(get_category_link($this->news_id))
        );
    ?>

    <?php wp_reset_query(); ?>
</div>
