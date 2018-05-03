<?php
/**
 * Stories archive
 *
 * Shows only story post type archive
 *
 * @package knife-theme
 * @since 1.3
 */

get_header(); ?>

<main class="wrap">

    <div class="content block">
<?php
    if(have_posts()) :

        while (have_posts()) : the_post();

            knife_theme_widget_template([
                'size' => 'story',
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
        <?php next_posts_link(__('Больше историй', 'knife-theme')); ?>
    </div>
<?php endif; ?>

</main>


<?php

get_footer();
