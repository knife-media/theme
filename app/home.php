<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="block-wrapper">
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
