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
                '<div class="entry-header__info">', '</div>',
                ['author', 'date', 'category', 'club', 'label']
            );

            the_title(
                '<h1 class="entry-header__title">',
                '</h1>'
            );

            the_lead(
                '<div class="entry-header__lead">',
                '</div>'
            );

            the_share(
                '<div class="entry-header__share share">', '</div>',
                __('Share post — top', 'knife-theme')
            );
        ?>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>

    <?php if(comments_open()) : ?>
        <div class="entry-comments">
            <?php
                printf(
                    '<button class="entry-comments__button button" id="load-comments">%s</button>',
                    __('Комментарии', 'knife-media')
                );
            ?>
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

    <?php if(get_the_archive_title()) : ?>
        <div class="entry-caption">
            <?php
                the_archive_title();
            ?>
        </div>
    <?php endif; ?>
</article>

<?php get_sidebar(); ?>
