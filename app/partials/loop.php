<?php
/**
 * Default archive loop partial template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */
?>

<article class="widget widget-<?php echo get_query_var('widget_size', 'triple'); ?>">
    <div class="widget__item">
        <?php
            the_info(
                '<div class="widget__head">', '</div>',
                ['tag']
            );
        ?>

        <div class="widget__image">
            <?php
                the_post_thumbnail(
                    get_query_var('widget_size', 'triple'),
                    ['class' => 'widget__image-thumbnail']
                );
            ?>
        </div>

        <footer class="widget__footer">
            <?php
                printf(
                    '<a class="widget__link" href="%2$s">%1$s</a>',
                    the_title('<p class="widget__title">', '</p>', false),
                    get_permalink()
                );

                the_info(
                    '<div class="widget__meta meta">', '</div>',
                    ['author', 'date']
                );
            ?>
        </footer>
    </div>
</article>

