<?php
/**
 * Standart post format content template with sidebar
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
                __('Share post — top', 'knife-theme')
            );
        ?>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>

    <div class="entry-footer">
        <?php
            if(comments_open()) {
                printf(
                    '<button class="button">%s</button>',
                    __('Комментарии', 'knife-media')
                );
            }
        ?>

        <div class="refers">
            <?php
                the_tags(
                    '<div class="tags">', null, '</div>'
                );

                the_share(
                    '<div class="share">', '</div>',
                    __('Share post — bottom', 'knife-theme'), true
                );
            ?>
        </div>
    </div>
</article>

<?php get_sidebar(); ?>
