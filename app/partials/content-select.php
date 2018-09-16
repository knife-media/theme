<?php
/**
 * Single select post type content template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-header">
        <?php
            the_title(
                '<h1 class="title">',
                '</h1>'
            );

            the_lead(
                '<div class="excerpt">',
                '</div>'
            );

            the_share(
                '<div class="share">', '</div>',
                __('Share select — top', 'knife-theme')
            );
        ?>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>

    <div class="entry-footer">
        <div class="refers">
            <?php
                the_share(
                    '<div class="share">', '</div>',
                    __('Share select — bottom', 'knife-theme'), true
                );
            ?>
        </div>
    </div>
</article>
