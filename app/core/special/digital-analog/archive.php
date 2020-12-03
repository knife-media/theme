<?php
/**
 * digital-analog special archive template
 *
 * @package knife-theme
 * @since 1.10
 */

get_header(); ?>

<div class="content">
    <div class="caption">
        <?php
            the_archive_title();
            the_archive_description();
        ?>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'units');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/special/digital-analog/footer');
