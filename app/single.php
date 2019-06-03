<?php
/**
 * Template for display single post
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.9
 */

get_header(); ?>

<?php if(is_active_sidebar('knife-feature')) : ?>
    <div class="block-wrapper">
        <?php
            dynamic_sidebar('knife-feature');
        ?>
    </div>
<?php endif; ?>

<div class="block-content">
    <?php
        while(have_posts()) : the_post();

            get_template_part('partials/content', get_post_format());

        endwhile;
    ?>
</div>

<?php get_footer();
