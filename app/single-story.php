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
        <?php get_template_part('template-parts/content/story'); ?>
    </div>

    <div class="content block">

    <?php
        $q = new WP_Query(['post_type' => 'story']);

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
