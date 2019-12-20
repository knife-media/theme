<?php
/**
 * Single select post type content template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.5
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-select">
        <?php
            the_title(
                '<h1 class="entry-select__title">',
                '</h1>'
            );

            the_lead(
                '<div class="entry-select__lead">',
                '</div>'
            );

            the_share(
                '<div class="entry-select__share share">',
                '</div>'
            );
        ?>

        <div class="entry-select__units">
            <?php
                the_content();
            ?>
        </div>
    </div>

    <?php if(comments_open()) : ?>
        <div class="entry-comments">
            <div class="comments" id="hypercomments_widget"></div>
        </div>
    <?php endif; ?>

    <div class="entry-footer">
        <?php
            the_share(
                '<div class="entry-footer__share share">',
                '</div>'
            );
        ?>
    </div>
</article>
