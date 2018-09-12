<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="content">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/iterate');

            endwhile;
        else :

            get_template_part('partials/content', 'none');

        endif;
    ?>
</div>

<?php get_footer();
