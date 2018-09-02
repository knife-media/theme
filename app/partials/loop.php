<?php
/**
 * Default archive loop partial template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */
?>

<article class="unit unit--<?php echo get_query_var('widget_size', 'triple'); ?>">
    <div class="unit__item">
        <?php
            the_info(
                '<div class="unit__head">', '</div>',
                ['tag']
            );
        ?>

        <div class="unit__image">
            <?php
                the_post_thumbnail(
                    get_query_var('widget_size', 'triple'),
                    ['class' => 'unit__image-thumbnail']
                );
            ?>
        </div>

        <footer class="unit__footer">
            <?php
                printf(
                    '<a class="unit__link" href="%2$s">%1$s</a>',
                    the_title('<p class="unit__title">', '</p>', false),
                    get_permalink()
                );

                the_info(
                    '<div class="unit__meta meta">', '</div>',
                    ['author', 'date']
                );
            ?>
        </footer>
    </div>
</article>
