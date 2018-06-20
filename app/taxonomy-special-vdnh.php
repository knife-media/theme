<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">
    <?php if(have_posts()) : ?>
    <div class="caption special">
        <img src="https://knife.media/wp-content/uploads/2018/06/vdnh-logo.png" style="width: 50px; height: 50px; margin-right: 1rem;">
        <h1><?php _e('ВДНХ &mdash; место силы','knife-theme'); ?></h1>
    </div>
    <?php endif; ?>


    <div class="content block">
    <?php
        if(have_posts()) :

            while (have_posts()) : the_post();

                knife_theme_widget_template([
                    'before' => '<div class="widget widget-%s">',
                    'after' => '</div>'
                ]);

            endwhile;

        else:

            // Include "no posts found" template
            get_template_part('template-parts/content/post', 'none');

        endif;
    ?>
    </div>


    <?php if(get_next_posts_link()) : ?>
    <div class="nav block">
        <?php next_posts_link(__('Больше статей', 'knife-theme')); ?>
    </div>
    <?php endif; ?>

</main>


<?php

get_footer('vdnh');
