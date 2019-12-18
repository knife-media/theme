<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.10
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

<div class="archive">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'units');

            endwhile;
        else :

            get_template_part('partials/message');

        endif;
    ?>
</div>

<?php if(have_posts()) : ?>
    <nav class="navigate">
        <?php
            previous_posts_link(__('Предыдущие', 'knife-theme'));
            next_posts_link(__('Следующие', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
