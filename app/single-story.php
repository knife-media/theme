<?php
/**
 * Single story
 *
 * @package knife-theme
 * @since 1.3
 */

get_header(); ?>

<main class="wrap">

    <div class="content">

        <div class="slider swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">

                    <div class="slider__item block">
                        <?php
                            knife_theme_meta([
                                'before' => '<div class="slider__item-meta meta">',
                                'after' => '</div>'
                            ]);

                            the_title(
                                '<h1 class="slider__item-title">',
                                '</h1>'
                            );

                            knife_theme_post_meta([
                                'before' => '<div class="slider__item-excerpt">',
                                'after' => '</div>',
                                'meta' => 'lead-text'
                            ]);
                        ?>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="content block">

    <?php
        $q = new WP_Query([
            'post_type' => 'story',
            'posts_per_page' => 16
        ]);

        if($q->have_posts()) :

            while ($q->have_posts()) : $q->the_post();

                knife_theme_widget_template([
                    'size' => 'story',
                    'before' => '<div class="widget widget-%s">',
                    'after' => '</div>'
                ]);

            endwhile;

        endif;
    ?>

    </div>

</main>


<?php

get_footer();
