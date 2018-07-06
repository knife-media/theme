<?php
/**
 * The main template file
 *
 * Most likely this template will never be shown.
 * It is used to display a page when nothing more specific matches a query.
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
