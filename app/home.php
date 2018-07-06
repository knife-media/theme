<?php
/**
 * The template for displaying main archive
 *
 * List of articles ordered by asc on replacement front page to show all posts.
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
