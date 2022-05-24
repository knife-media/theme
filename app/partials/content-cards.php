<?php
/**
 * Cards no-sidebar post template content template
 *
 * @package knife-theme
 * @since 1.12
 * @version 1.16
 */
?>

<article <?php post_class('post post--cards'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-header">
        <?php
            the_info(
                '<div class="entry-header__info">', '</div>',
                ['author', 'date', 'best']
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
                '<div class="entry-header__share share">',
                '</div>'
            );
        ?>
    </div>

    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>

    <?php if(is_active_sidebar('knife-inpost')) : ?>
        <div class="entry-inpost">
            <?php
                dynamic_sidebar('knife-inpost');
            ?>
        </div>
    <?php endif; ?>

    <?php if(comments_open()) : ?>
        <div class="entry-comments">
            <div class="comments" id="comments"></div>
        </div>
    <?php endif; ?>

    <div class="entry-footer">
        <?php
            the_tags(
                '<div class="entry-footer__tags tags">', '', '</div>'
            );

            the_share(
                '<div class="entry-footer__share share">',
                '</div>'
            );
        ?>
    </div>

    <?php if(is_active_sidebar('knife-bottom')) : ?>
        <div class="entry-bottom">
            <?php
                dynamic_sidebar('knife-bottom');
            ?>
        </div>
    <?php endif; ?>

    <?php
        the_tagline(
            '<div class="entry-caption">', '</div>'
        );
    ?>
</article>
