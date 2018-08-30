<?php
/**
 * News archive loop partial template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<article class="widget widget-logbook">

    <div class="widget__item">
        <footer class="widget__footer">
            <?php
                printf(
                    '<a class="widget__link" href="%2$s">%1$s</a>',
                    the_title('<p class="widget__title">', '</p>', false),
                    get_permalink()
                );

                the_info(
                    '<div class="widget__meta meta">', '</div>',
                    ['time', 'date', 'tags']
                );
            ?>
        </footer>
    </div>

</article>
