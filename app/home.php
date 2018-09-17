<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="block-wrapper">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/loop');

            endwhile;
        else :

            get_template_part('partials/message');

        endif;
    ?>
</div>

<?php get_footer();
