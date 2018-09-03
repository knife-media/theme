<?php
/**
 * News archive loop partial template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<article class="news">

    <div class="news__item">
        <footer class="news__footer">
            <?php
                printf(
                    '<a class="news__link" href="%2$s">%1$s</a>',
                    the_title('<p class="news__title">', '</p>', false),
                    get_permalink()
                );

                the_info(
                    '<div class="news__meta meta">', '</div>',
                    ['time', 'date', 'tags']
                );
            ?>
        </footer>
    </div>

</article>
