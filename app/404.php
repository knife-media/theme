<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">
    <?php
        // Include "no posts found" template
        get_template_part('templates/content', 'none');
    ?>
</main>


<?php get_footer();
