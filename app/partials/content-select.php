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
        <div class="entry-footer__inner">
            <?php
                the_share(
                    '<div class="entry-footer__share share">', '</div>',
                    __('Share select — bottom', 'knife-theme')
                );
            ?>
        </div>
    </div>
</article>
