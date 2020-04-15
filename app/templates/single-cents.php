<?php
/**
 * Single template for cents page only
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<div class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'cents');

        endwhile;
    ?>
</div>

<?php get_footer();
