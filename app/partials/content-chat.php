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
                '<div class="entry-header__meta meta">', '</div>',
                ['author', 'date', 'category', 'type']
            );

            the_title(
                '<h1 class="entry-header__title">',
                '</h1>'
            );

            the_lead(
                '<div class="entry-header__excerpt excerpt">',
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

    <div class="entry-footer">
        <div class="entry-footer__inner">
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
    </div>

    <?php if(get_the_archive_title()) : ?>
        <div class="entry-caption">
            <?php
                the_archive_title();
            ?>
        </div>
    <?php endif; ?>
</article>

