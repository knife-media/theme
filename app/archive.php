<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.9
 */

get_header(); ?>

<?php if(have_posts() && get_the_archive_title()) : ?>
    <div class="block-caption">
        <?php
            the_archive_title();
            the_archive_description();
        ?>
    </div>
<?php endif; ?>

<div class="block-archive">
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
    <nav class="block-navigate">
        <?php
            next_posts_link(__('Больше статей', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
