<?php
/**
 * Template for display single post
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

                get_template_part('templates/content', get_post_format());

            endwhile;
        ?>

    </section>
</main>

<?php get_footer();
