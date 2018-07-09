<?php
/**
 * Author posts template
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">
    <?php
        if (have_posts()) :

            // Include specific archive template
            the_template('archive');

        else:

            // Include "no posts found" template
            the_template('message');

        endif;
    ?>
</main>


<?php get_footer();
