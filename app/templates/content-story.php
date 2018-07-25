<?php
/**
 * Story content template
 *
 * @package knife-theme
 * @since 1.3
 */
?>

<section class="content glide">

    <?php while (have_posts()) : the_post(); ?>
        <div class="glide__track" data-glide-el="track">

            <article class="glide__slides">
                <div class="glide__slide">
                    <div class="glide__slide-wrap glide__slide-wrap--first">
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
                                __('Share story — top', 'knife-theme')
                            );
                        ?>
                    </div>
                </div>
            </article>

        </div>
    <?php endwhile; ?>

    <svg class="glide__loader" x="0" y="0" viewBox="0 0 111 31.8" xml:space="preserve">
        <g>
            <path d="M27.4,0.6v30.7h-8V19.1H8v12.2H0V0.6h8v11.4h11.4V0.6H27.4z"></path>
            <path d="M63.4,15.9C63.4,25,58,31.8,48,31.8c-9.9,0-15.4-6.8-15.4-15.9C32.7,6.8,38.1,0,48,0
                C58,0,63.4,6.8,63.4,15.9z M55.2,15.9c0-5.2-2.4-8.9-7.2-8.9s-7.2,3.7-7.2,8.9c0,5.2,2.4,8.9,7.2,8.9S55.2,21.1,55.2,15.9z"></path>
            <path d="M84.9,0.6h7.7v11.5H98l4.6-11.5h8l-6.1,15.1l6.5,15.6h-8l-4.9-12h-5.4v12h-7.7v-12h-5.4l-4.9,12h-8
                l6.5-15.6L67,0.6h8l4.6,11.5h5.3V0.6z"></path>
        </g>
    </svg>

</section>

<section class="content block">

<?php
    $q = new WP_Query(['posts_per_page'=> 4, 'post_type' => 'story']);
?>
    <?php while ($q->have_posts()) : $q->the_post(); ?>
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

</section>
