<?php
/**
 * Select widget template
 *
 * @package knife-theme
 * @since 1.11
 */
?>

<div class="widget-select__wrapper">
    <?php
        printf('<a class="widget-select__head head" href="%2$s">%1$s</a>',
            esc_html($instance['title']),
            esc_url(get_post_type_archive_link('select'))
        );

        while($query->have_posts()) {
            $query->the_post();

            printf(
                '<a class="widget-select__link" href="%1$s">%2$s</a>',
                esc_url(get_permalink()),
                get_the_title()
            );
        }

        wp_reset_query();
    ?>
</div>
