<?php
/**
 * Lighthouse: single template
 *
 * @package knife-theme
 * @since 1.10
 */

get_header(); ?>

<div class="content">
    <?php while(have_posts()) : the_post(); ?>

        <div class="caption">
            <div class="caption__description">
                <?php
                    printf(
                        '<img src="%s" alt="">',
                        get_template_directory_uri() . "/special/lighthouse/images/logo.png",
                    );

                    printf(
                        '<a href="%s">%s</a>',
                        get_site_url(null, '/special/lighthouse/'),
                        _x('Дом с маяком', 'special: lighthouse', 'knife-theme')
                    );
                ?>
            </div>
        </div>

        <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
            <div class="entry-header">
                <?php
                    the_title(
                        '<h1 class="entry-header__title">',
                        '</h1>'
                    );

                    the_lead(
                        '<div class="entry-header__lead">',
                        '</div>'
                    );

                    the_share(
                        '<div class="entry-header__share share">', '</div>',
                        __('Share aside — top', 'knife-theme')
                    );
                ?>
            </div>

            <div class="entry-content">
                <?php
                    the_content();
                ?>
            </div>

            <?php if(comments_open()) : ?>
                <div class="entry-comments">
                    <div class="comments" id="hypercomments_widget"></div>
                </div>
            <?php endif; ?>
        </article>

    <?php endwhile; ?>
</div>

<?php get_footer();
