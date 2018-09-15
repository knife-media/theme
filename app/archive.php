<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<?php if(have_posts() && get_the_archive_title()) : ?>
    <div class="caption">
        <?php
            the_archive_title();

            the_archive_description();
        ?>
    </div>
<?php endif; ?>

<div class="wrapper">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/loop');

            endwhile;
        else :

            get_template_part('partials/message');

        endif;
    ?>
</div>

<?php if(have_posts() && get_next_posts_link()) : ?>
    <nav class="navigation">
        <?php
            next_posts_link(__('Больше статей', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
