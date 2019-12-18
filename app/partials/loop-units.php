<?php
/**
 * Default archive loop partial template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.9
 */
?>

<div class="unit unit--<?php echo get_query_var('widget_size', 'triple'); ?>">
    <div class="unit__inner">
        <?php
            the_info(
                '<div class="unit__head info">', '</div>',
                ['head']
            );
        ?>

        <div class="unit__image">
            <?php
                the_post_thumbnail(
                    get_query_var('widget_size', 'triple'),
                    ['class' => 'unit__image-thumbnail', 'loading' => 'lazy']
                );
            ?>
        </div>

        <div class="unit__content">
            <?php
                printf(
                    '<a class="unit__content-link" href="%1$s">%2$s</a>',
                    esc_url(get_permalink()),
                    get_the_title()
                );

                the_info(
                    '<div class="unit__content-info info">', '</div>',
                    ['author', 'date', 'label']
                );
            ?>
        </div>
    </div>
</div>
