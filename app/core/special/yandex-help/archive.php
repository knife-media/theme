<?php
/**
 * special archive template
 * @package knife-theme
 * @since 1.14
 */

get_header(); ?>

<?php if(have_posts() && get_the_archive_title()) : ?>
    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<img src="%s" alt="">',
                    get_template_directory_uri() . '/core/special/yandex-help/images/logo.svg',
                );

                printf(
                    '<h1>%s</h1>',
                    _x('Совместный проект социального проекта Яндекса «Помощь рядом» и журнала «Нож»', 'special: yandex-help', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x('«Помощь рядом» предоставляет благотворительным фондам бесплатный доступ к сервисам и технологиям Яндекса. Поддержите работу проекта — а в феврале Яндекс добавит в копилку ещё 5 миллионов рублей.', 'special: yandex-help', 'knife-theme')
                );
            ?>
        </div>

        <div class="caption__button">
            <?php
                printf(
                    '<a class="button" href="https://knife.media" target="_blank" rel="noopener">%s</a>',
                    _x('Хочу помочь', 'special: yandex-help', 'knife-theme')
                );
            ?>
        </div>
    </div>
<?php endif; ?>

<div class="archive">
   <?php
        if(have_posts()) :
            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'units');

            endwhile;
        else :

            get_template_part('partials/message');

        endif;
    ?>
</div>

<?php get_footer();
