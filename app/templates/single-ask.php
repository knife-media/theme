<?php
/**
 * Single template for ask post type
 *
 * @package knife-theme
 * @since 1.7
 */

get_header(); ?>

<div class="block-content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'ask');

        endwhile;
    ?>
</div>

<?php get_footer();
