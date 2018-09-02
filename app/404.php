<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content">
        <?php
            get_template_part('partials/content', 'none');
        ?>
    </section>
</div>

<?php get_footer();
