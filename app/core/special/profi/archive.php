<?php
/**
 * profi: archive template
 * @package knife-theme
 * @since 1.14
 */

get_header(); ?>

<div class="archive">
    <div class="archive__caption">
        <?php
            printf(
                '<img src="%s" alt="">',
                get_template_directory_uri() . '/core/special/profi/images/profi-logo.png',
            );

            printf(
                '<h1>%s</h1>',
                _x('<strong>Совместный проект</strong> платформы олимпиады «Я — профессионал» и журнала «Нож»', 'special: profi', 'knife-theme')
            );
        ?>
    </div>

    <?php
        while(have_posts()) : the_post();

            get_template_part('core/special/profi/loop');

        endwhile;
    ?>
</div>

<?php get_footer();
