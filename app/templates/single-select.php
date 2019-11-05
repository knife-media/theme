<?php
/**
 * Single template for select post type
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.10
 */

get_header(); ?>

<div class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'select');

        endwhile;
    ?>
</div>

<?php get_footer();
