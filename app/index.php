<?php
/**
 * The main template file
 *
 * Most likely this template will never be shown.
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.10
 */

get_header();

// Add 404 message template part
get_template_part('partials/message');

get_footer();
