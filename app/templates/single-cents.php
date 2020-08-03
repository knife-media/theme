<?php
/**
 * Single template for cents page only
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<section class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'cents');

        endwhile;
    ?>
</section>

<?php get_footer();
