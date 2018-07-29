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
            the_archive_title('<h1 class="caption__title">', '</h1>');

            the_archive_description('<div class="caption__text">', '</div>');
        ?>
    </header>

    <section class="content block">

       <?php
            while(have_posts()) : the_post();

                get_template_part('partials/archive');

            endwhile;
        ?>

    </section>
</div>

<?php get_footer();
