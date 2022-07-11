<?php
/**
 * Template Name: Широкий
 * Template Post Type: post
 *
 * @package knife-theme
 * @since 1.16
 */

get_header(); ?>

<section class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'wide');

        endwhile;
    ?>
</section>

<?php get_footer();
