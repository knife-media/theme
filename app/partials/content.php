<?php
/**
 * Standart post format content template with sidebar
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.12
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-content">
        <?php
            the_info(
                '<div class="entry-content__info">', '</div>',
                ['club', 'author', 'date', 'best']
            );

            the_title(
                '<h1 class="entry-content__title">',
                '</h1>'
            );

            the_lead(
                '<div class="entry-content__lead">',
                '</div>'
            );

            the_share(
                '<div class="entry-content__share share">',
                '</div>'
            );

            echo '<div class="entry-content__editor editor">';
            the_content();
            echo '</div>';
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
            <div class="comments" id="hypercomments_widget"></div>
        </div>
    <?php endif; ?>

    <div class="entry-footer">
        <?php
            the_tags(
                '<div class="entry-footer__tags tags">', null, '</div>'
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

<?php get_sidebar(); ?>
