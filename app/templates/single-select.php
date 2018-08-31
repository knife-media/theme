<?php
/**
 * Single template for select post type
 *
 * @package knife-theme
 * @since 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/content', 'select');

            endwhile;
        ?>
    </section>
</div>

<?php get_footer();
