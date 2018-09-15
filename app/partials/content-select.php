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
        <div class="entry-header__inner">
            <?php
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
                    __('Share select — top', 'knife-theme')
                );
            ?>
        </div>
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
                    __('Share select — bottom', 'knife-theme'), true
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
