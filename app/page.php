<?php
/**
 * Page template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="block-content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'page');

        endwhile;
    ?>
</div>

<?php get_footer();
