<?php
/**
 * Single story template
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
                        ?>

                        <div class="slider__item-entry">
                            <?php
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

    </div>

    <?php
        $query = new WP_Query([
            'post_type' => 'story',
            'posts_per_page' => 4
        ]);
    ?>

    <div class="content block">
    <?php
        if($query->have_posts()) :

            while ($query->have_posts()) : $query->the_post();

                knife_theme_widget_template([
                    'size' => 'story',
                    'before' => '<div class="widget widget-%s">',
                    'after' => '</div>'
                ]);

            endwhile;

        endif;
    ?>
    </div>

    <?php
        if($query->have_posts()) :

          printf('<div class="nav block"><a class="button" href="%2$s">%1$s</a></div>',
            __('Все истории', 'knife-theme'),
            esc_url(get_post_type_archive_link('story'))
          );
        endif;
    ?>

</main>


<?php

get_footer();
