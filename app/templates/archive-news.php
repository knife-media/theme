<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="content narrow">

    <?php while (have_posts()) : the_post(); ?>
        <article class="widget widget-logbook">

            <div class="<?php knife_theme_widget_options('widget__item'); ?>">
                <?php if(get_post_meta($post->ID, '_knife-cover', true) && has_post_thumbnail()) : ?>
                <div class="widget__image">
                    <?php the_post_thumbnail('thumbnail', ['class' => 'widget__image-thumbnail']); ?>
                </div>
                <?php endif; ?>

                <footer class="widget__footer">
                    <?php
                        printf(
                            '<a class="widget__link" href="%2$s">%1$s</a>',
                            the_title('<p class="widget__title">', '</p>', false),
                            get_permalink()
                        );

                        knife_theme_meta([
                            'opts' => ['time', 'date', 'tags'],
                            'before' => '<div class="widget__meta meta">',
                            'after' => '</div>'
                        ]);
                    ?>
                </footer>
            </div>

        </article>
    <?php endwhile; ?>

</section>
