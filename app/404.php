<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

    <div class="content block">

    <?php
        // Include "no posts found" template
        get_template_part('template-parts/content/post', 'none');
    ?>

    </div>

</main>


<?php

get_footer();
