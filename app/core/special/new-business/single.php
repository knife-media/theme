<?php
/**
 * special single template
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<div class="content">
    <?php while(have_posts()) : the_post(); ?>

        <div class="caption">
            <div class="caption__description">
                <?php
                    printf(
                        '<a href="%s">%s</a>',
                        get_term_link('new-business', 'special'),
                        _x('Это бизнес. И это личное', 'special: new-business', 'knife-theme')
                    );
                ?>
            </div>
        </div>

        <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
            <div class="entry-header">
                <?php
                    printf(
                        '<h1 class="entry-header__title"><strong>%s</strong>%s</h1>',
                        get_post_meta(get_the_ID(), 'post-hero', true),

                        sprintf(
                            '<a href="%s" target="_blank" rel="noopener">%s</a>',
                            get_post_meta(get_the_ID(), 'post-link', true),
                            get_post_meta(get_the_ID(), 'post-info', true)
                        )
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

                    printf(
                        '<div class="entry-content__author"><span>%s</span></div>',
                        _x('Подготовила Агата Коровина', 'special: new-business', 'knife-theme')
                    );
                ?>
            </div>
        </article>

        <nav class="navigate">
            <?php
                previous_post_link('%link', '%title', true, '', 'special');
                next_post_link('%link', '%title', true, '', 'special');
            ?>
        </nav>

    <?php endwhile; ?>
</div>

<?php get_template_part('core/special/new-business/footer');
