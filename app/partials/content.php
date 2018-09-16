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
        <div class="entry-header__inner">
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
                    '<div class="entry-header__excerpt">',
                    '</div>'
                );

                the_share(
                    '<div class="entry-header__share share">', '</div>',
                    __('Share post — top', 'knife-theme')
                );
            ?>
        </div>
    </div>

    <div class="entry-content article">
        <div class="entry-content__inner">
            <?php
                the_content();
            ?>
        </div>
    </div>

    <?php if(comments_open()) : ?>
        <div class="entry-board">
            <?php
                printf(
                    '<button class="button">%s</button>',
                    __('Комментарии', 'knife-media')
                );
            ?>
        </div>
    <?php endif; ?>

    <div class="entry-footer">
        <div class="entry-footer__inner">
            <?php
                the_tags(
                    '<div class="entry-footer__tags refers">',
                    null, '</div>'
                );

                the_share(
                    '<div class="entry-footer__share share">', '</div>',
                    __('Share post — bottom', 'knife-theme'), true
                );
            ?>
        </div>
    </div>

    <?php if(is_active_sidebar('knife-inside')) : ?>
        <div class="entry-widgets">
            <?php
                dynamic_sidebar('knife-inside');
            ?>
        </div>
    <?php endif; ?>
</article>

<?php get_sidebar(); ?>
