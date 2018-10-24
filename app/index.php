<?php
/**
 * The main template file
 *
 * Most likely this template will never be shown.
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package knife-theme
 * @since 1.5
 */

get_header(); ?>

<div class="block-wrapper">
    <?php
        get_template_part('partials/message');
    ?>
</div>

<?php get_footer();
