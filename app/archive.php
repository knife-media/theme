<?php
/**
 * The template for displaying archive pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="wrap">
    <?php if(have_posts() && get_the_archive_title()) : ?>
        <header class="caption">
            <?php
                the_archive_title();

                the_archive_description();
            ?>
        </header>
    <?php endif; ?>

    <section class="content">
       <?php
            if(have_posts()) :
                while(have_posts()) : the_post();

                    get_template_part('partials/iterate');

                endwhile;
            else :

                get_template_part('partials/content', 'none');

            endif;
        ?>
    </section>

    <?php if(have_posts() && get_next_posts_link()) : ?>
        <nav class="navigation">
            <?php
                next_posts_link(__('Больше статей', 'knife-theme'));
            ?>
        </nav>
    <?php endif; ?>
</div>

<?php get_footer();
