<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content block block--narrow">
       <?php
            if(have_posts()) :
                while(have_posts()) : the_post();

                    get_template_part('partials/loop', 'news');

                endwhile;
            else :

                get_template_part('partials/content', 'none');

            endif;
        ?>
    </section>

    <?php if(have_posts() && get_next_posts_link()) : ?>
        <nav class="nav block block--narrow">
            <?php
                next_posts_link(__('Больше историй', 'knife-theme'));
            ?>
        </nav>
    <?php endif; ?>
</div>

<?php get_footer();
