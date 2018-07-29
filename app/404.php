<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<main class="wrap">
    <section class="content block">

        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/content', 'none');

            endwhile;
        ?>

    </section>
</main>

<?php get_footer();
