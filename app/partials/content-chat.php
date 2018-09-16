<?php
/**
 * Cards no-sidebar post format content template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-header">
        <?php
            the_info(
                '<div class="meta">', '</div>',
                ['author', 'date', 'category', 'type']
            );

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
                __('Share cards — top', 'knife-theme')
            );
        ?>
    </div>

    <?php
        the_content();
    ?>

    <div class="entry-footer">
        <div class="refers">
            <?php
                the_tags(
                    '<div class="tags">', null, '</div>'
                );

                the_share(
                    '<div class="share">', '</div>',
                    __('Share chat — bottom', 'knife-theme'), true
                );
            ?>
        </div>
    </div>
</article>

