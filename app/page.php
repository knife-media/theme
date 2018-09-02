<?php
/**
 * Page template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content content--post">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/content', 'page');

            endwhile;
        ?>
    </section>
</div>

<?php get_footer();
