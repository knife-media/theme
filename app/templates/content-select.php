<?php
/**
 * Standart post format content template width sidebar
 *
 * @package knife-theme
 * @since 1.1
 */
?>


<section class="content block">

    <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">

        <header class="post__header">
            <?php
                the_title(
                    '<h1 class="post__header-title">',
                    '</h1>'
                );

                the_lead(
                    '<div class="post__header-excerpt custom">',
                    '</div>'
                );

                the_share(
                    '<div class="post__header-share share">',
                    '</div>',
                    __('Share post — top', 'knife-theme')
                );
            ?>
        </header>

        <div class="post__content custom">
            <?php the_content(); ?>
        </div>

        <footer class="post__footer">
            <?php
                the_share(
                    '<div class="post__footer-share share">',
                    '</div>',
                    __('Share post — bottom', 'knife-theme'),
                    __('Поделиться в соцсетях:', 'knife-theme')
                );
            ?>

            <?php if(is_active_sidebar('knife-post-widgets')) : ?>
              <div class="post__footer-widgets">
                <?php dynamic_sidebar('knife-post-widgets'); ?>
              </div>
            <?php endif; ?>
        </footer>

    </article>

</section>

