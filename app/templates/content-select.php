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

                knife_theme_post_meta([
                    'before' => '<div class="post__header-excerpt custom">',
                    'after' => '</div>',
                    'meta' => 'lead-text',
                    'filter' => true
                ]);

                knife_theme_share([
                    'before' => '<div class="post__header-share share">',
                    'after' => '</div>',
                    'title' => '',
                    'action' => 'Share post — top'
                ]);
            ?>
        </header>

        <div class="post__content custom">
            <?php the_content(); ?>
        </div>

        <footer class="post__footer">
            <?php
                knife_theme_share([
                    'before' => '<div class="post__footer-share share">',
                    'after' => '</div>',
                    'action' => 'Share post — bottom'
                ]);
            ?>

           <?php if(is_active_sidebar('knife-post-widgets')) : ?>
              <div class="post__footer-widgets">
                <?php dynamic_sidebar('knife-post-widgets'); ?>
              </div>
            <?php endif; ?>
        </footer>
    </article>

    <?php get_sidebar(); ?>
</section>

