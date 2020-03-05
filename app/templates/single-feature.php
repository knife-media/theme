<?php
/**
 * Template Name: Избранное
 * Template Post Type: post
 *
 * Template for displaying featured post without sidebar
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

<div class="content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', 'feature');

        endwhile;
    ?>
</div>

<?php get_footer();
