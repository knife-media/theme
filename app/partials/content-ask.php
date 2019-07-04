<?php
/**
 * Single quiz post type content template
 *
 * @package knife-theme
 * @since 1.7
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-ask">
        <div class="entry-ask__content">
            <?php
                the_info(
                    '<div class="entry-ask__date info">', '</div>',
                    ['date']
                );

                the_title(
                    '<h1 class="entry-ask__title">',
                    '</h1>'
                );

                the_lead(
                    '<div class="entry-ask__lead">',
                    '</div>'
                );

                the_info(
                    '<div class="entry-ask__asker info">', '</div>',
                    ['asker']
                );
            ?>
        </div>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>

    <?php if(comments_open()) : ?>
        <div class="entry-comments">
            <div class="comments" id="hypercomments_widget"></div>
        </div>
    <?php endif; ?>

    <div class="entry-footer">
        <?php
            the_tags(
                '<div class="entry-footer__tags tags">', null, '</div>'
            );

            the_share(
                '<div class="entry-footer__share share">', '</div>',
                __('Share post — bottom', 'knife-theme')
            );
        ?>
    </div>
</article>

<?php get_sidebar(); ?>
