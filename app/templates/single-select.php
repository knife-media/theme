<?php
/**
 * Single template for select post type
 *
 * @package knife-theme
 * @since 1.4
 */

get_header(); ?>

<div class="wrap">
    <section class="content block">

        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('post post--select'); ?> id="post-<?php the_ID(); ?>">
                <header class="post__header">
                    <?php
                        the_title(
                            '<h1 class="post__header-title">',
                            '</h1>'
                        );

                        the_lead(
                            '<div class="post__header-excerpt">',
                            '</div>'
                        );

                        the_share(
                            '<div class="post__header-share share">',
                            '</div>',
                            __('Share select — top', 'knife-theme')
                        );
                    ?>
                </header>

                <div class="post__content">
                    <?php the_content(); ?>
                </div>

                <footer class="post__footer">
                    <?php
                        the_share(
                            '<div class="post__footer-share share">',
                            '</div>',
                            __('Share select — bottom', 'knife-theme'),
                            __('Поделиться в соцсетях:', 'knife-theme')
                        );

                        the_sidebar(
                            'knife-post-widgets',
                            '<div class="post__footer-widgets">',
                            '</div>'
                        );
                    ?>
                </footer>
            </article>
        <?php endwhile; ?>

    </section>
</div>

<?php get_footer();