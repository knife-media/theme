<?php
/**
 * Story content template
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="content glide">

    <div class="glide__loader">
        <span class="glide__loader-bounce"></span>
    </div>

    <?php while (have_posts()) : the_post(); ?>
        <div class="glide__track" data-glide-el="track">

            <article class="glide__slides">
                <div class="glide__slide">
                    <div class="glide__slide-wrap glide__slide-wrap--head">
                        <div class="glide__slide-content">
                            <?php
                                the_info(
                                    '<div class="glide__slide-meta meta">', '</div>',
                                    ['author', 'date']
                                );

                                the_title(
                                    '<h1 class="glide__slide-title">',
                                    '</h1>'
                                );

                                the_lead(
                                    '<div class="glide__slide-excerpt">',
                                    '</div>'
                                );

                                the_share(
                                    '<div class="glide__slide-share share">',
                                    '</div>',
                                    __('Share story — first', 'knife-theme')
                                );
                            ?>
                        </div>
                    </div>
                </div>
            </article>

        </div>
    <?php endwhile; ?>

</section>


<section class="content block">
    <?php
        $query = new WP_Query([
            'post_type' => 'story',
            'post__not_in' => [$post->ID],
            'posts_per_page' => 4
        ]);
    ?>

    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <article class="widget widget-story">

            <div class="widget__item">
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
                    ?>
                </footer>
            </div>

        </article>
    <?php endwhile; wp_reset_query(); ?>

    <?php
        if($query->have_posts()) {
          printf('<a class="button" href="%2$s">%1$s</a>',
            __('Все истории', 'knife-theme'),
            esc_url(get_post_type_archive_link('story'))
          );
        }
    ?>

</section>
