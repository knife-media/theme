<?php
/**
 * Single template for generator post type
 *
 * @package knife-theme
 * @since 1.6
 */

get_header(); ?>

<div class="block-content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'generator');

        endwhile;
    ?>
</div>

<?php get_footer();
