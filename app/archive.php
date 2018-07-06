<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">
    <?php
        if (have_posts()) :

            // Include specific content template
            get_template_part('templates/archive');

        else:

            // Include "no posts found" template
            get_template_part('templates/content', 'none');

        endif;
    ?>
</main>

<?php get_footer();
