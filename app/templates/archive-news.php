<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */

get_header(); ?>

<div class="wrapper">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'news');

            endwhile;
        else :

            get_template_part('message');

        endif;
    ?>
</div>

<?php if(have_posts() && get_next_posts_link()) : ?>
    <nav class="navigation">
        <?php
            next_posts_link(__('Больше новостей', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
