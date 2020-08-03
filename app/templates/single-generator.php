<?php
/**
 * Single template for generator post type
 *
 * @package knife-theme
 * @since 1.6
 * @version 1.10
 */

get_header(); ?>

<section class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'generator');

        endwhile;
    ?>
</section>

<?php get_footer();
