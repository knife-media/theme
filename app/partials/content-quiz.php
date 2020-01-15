<?php
/**
 * Single quiz post type content template
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.11
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-quiz" id="quiz">
        <?php
            the_info(
                '<div class="entry-quiz__info info">', '</div>',
                ['author', 'date', 'label']
            );

            the_title(
                '<h1 class="entry-quiz__title">',
                '</h1>'
            );

            the_share(
                '<div class="entry-quiz__share share">',
                '</div>'
            );

            the_lead(
                '<div class="entry-quiz__content">',
                '</div>'
            );
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
