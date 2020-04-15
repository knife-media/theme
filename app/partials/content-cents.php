<?php
/**
 * Single cents page content template
 *
 * @package knife-theme
 * @since 1.12
 */
?>

<article <?php post_class('post post--cents'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-header">
        <?php
            the_title(
                '<h1 class="entry-header__title">',
                '</h1>'
            );

            the_content();
        ?>
    </div>
</article>