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
                        get_term_link('lighthouse', 'special'),
                        _x('Дом с маяком', 'special: lighthouse', 'knife-theme')
                    );
                ?>
            </div>
        </div>

        <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
            <div class="entry-header">
                <?php
                    printf(
                        '<p class="entry-header__emoji">%s</p>',
                        get_post_meta(get_the_ID(), 'post-emoji', true)
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

                    the_info(
                        sprintf(
                            '<div class="entry-content__author"><p>%s</p>',
                            _x('Подготовила специальный<br> корреспондент журнала «Нож»', 'special: lighthouse', 'knife-theme')
                        ),
                        '</div>', ['author']
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

<?php get_template_part('special/lighthouse/footer');
