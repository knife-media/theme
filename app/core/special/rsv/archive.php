<?php
/**
 * special archive template *
 * @package knife-theme
 * @since 1.14
 */

get_header(); ?>

<div class="caption">
    <div class="caption__description">
        <?php
            printf(
                '<h1>%s</h1>',
                _x('<strong>Совместный проект</strong> платформы «Россия — страна возможностей» и журнала «Нож»', 'special: rsv', 'knife-theme')
            );

            printf(
                '<img src="%s" alt="">',
                get_template_directory_uri() . '/core/special/rsv/images/rsv-logo.png',
            );
        ?>
    </div>
</div>

<div class="archive">
    <?php
        while(have_posts()) : the_post();

            get_template_part('core/special/rsv/loop');

        endwhile;
    ?>
</div>

<?php get_footer();
