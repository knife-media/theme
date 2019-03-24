<?php
/**
 * Cards no-sidebar post format content template
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.8
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-header">
        <?php
            the_info(
                '<div class="entry-header__info">', '</div>',
                ['promo', 'author', 'date', 'category', 'label']
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
                __('Share cards — top', 'knife-theme')
            );
        ?>
    </div>

    <?php
        the_content();
    ?>

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
                __('Share cards — bottom', 'knife-theme')
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

