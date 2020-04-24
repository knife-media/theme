<?php
/**
 * Single cents page content template
 *
 * @package knife-theme
 * @since 1.12
 */
?>

<article <?php post_class('post post--cents'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-cents">
        <?php
            the_title(
                '<h1 class="entry-cents__title">',
                '</h1>'
            );

            the_lead(
                '<div class="entry-cents__lead">',
                '</div>'
            );
        ?>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>
</article>