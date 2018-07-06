<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="content block block--narrow">

    <?php while (have_posts()) : the_post(); ?>
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
    <?php endwhile; ?>

</section>
