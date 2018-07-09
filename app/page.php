<?php
/**
 * Page template
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">
    <?php
        if (have_posts()) :
            while (have_posts()) : the_post();

                // Include specific content template
                the_template('content', 'page');

            endwhile;
        else:

            // Include "no posts found" template
            the_template('message');

        endif;
    ?>
</main>

<?php get_footer();
