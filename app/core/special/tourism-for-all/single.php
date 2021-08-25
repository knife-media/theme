<?php
/**
 * special single template
 *
 * @package knife-theme
 * @since 1.15
 */

get_header(); ?>

<div class="content">
    <?php while(have_posts()) : the_post(); ?>

        <div class="caption">
            <div class="caption__description">
                <?php
                    printf(
                        '<a href="%s">%s</a>',
                        get_term_link('tourism-for-all', 'special'),
                        _x('Путешествовать с инвалидностью можно. Кто в России делает туризм доступным', 'special: tourism-for-all', 'knife-theme')
                    );
                ?>
            </div>
        </div>

        <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
            <div class="entry-header">
                <?php
                    printf(
                        '<h1 class="entry-header__title">%s</h1>',
                        get_the_title()
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
                            '<h5>%s', _x('Автор материала', 'special: death-work', 'knife-theme')
                        ),
                        '</h5>', ['author']
                    );
                ?>
            </div>
        </article>

    <?php endwhile; ?>
</div>

<?php get_template_part('core/special/tourism-for-all/footer');
