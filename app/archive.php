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
    <header class="caption block">
        <?php
            the_archive_title();

            the_archive_description();
        ?>
    </header>

    <section class="content block">
       <?php
            if(have_posts()) :
                while(have_posts()) : the_post();

                    get_template_part('partials/archive');

                endwhile;
            else :

                get_template_part('partials/content', 'none');

            endif;
        ?>
    </section>

    <nav class="nav block">
        <?php
            if(get_next_posts_link()) :

                next_posts_link(__('Больше статей', 'knife-theme'));

            endif;
        ?>
    </nav>
</div>

<?php get_footer();
