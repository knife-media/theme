<?php
/**
 * Template for display single post
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/content', get_post_format());

            endwhile;
        ?>
    </section>
</div>

<?php get_footer();
