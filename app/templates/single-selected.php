<?php
/**
 * Template Name: Избранное
 * Template Post Type: post
 *
 * Template for displaying selected post without sidebar
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<?php
    if(is_active_sidebar('knife-feature')) :
        dynamic_sidebar('knife-feature');
    endif;
?>

<section class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'selected');

        endwhile;
    ?>
</section>

<?php get_footer();
