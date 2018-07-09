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
        the_template('message');
    ?>
</main>


<?php get_footer();
