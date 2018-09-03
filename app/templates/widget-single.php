<?php
/**
 * Single widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-single">
    <?php
        the_info(
            '<div class="widget-single__head meta">', '</div>',
            ['tag']
        );
    ?>

    <div class="widget-single__image">
        <?php
            the_post_thumbnail(
                get_query_var('widget_size', 'single'),
                ['class' => 'widget-single__image-thumbnail']
            );
        ?>
    </div>

    <div class="widget-single__content">
        <?php
            printf(
                '<a class="widget-single-content-title" href="%1$s">%2$s</a>',
                esc_html(get_permalink()),
                get_the_title()
            );

            the_info(
                '<div class="widget-single__content-meta meta">', '</div>',
                ['author', 'date']
            );
        ?>
    </div>
</div>
