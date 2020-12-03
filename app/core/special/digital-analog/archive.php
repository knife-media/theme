<?php
/**
 * digital-analog special archive template
 *
 * @package knife-theme
 * @since 1.10
 */

get_header(); ?>

<div class="content">

    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<h1>%s</h1>',
                    _x('Слияние полярностей', 'special: digital-analog', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x(
                        'Спецпроект журнала «Нож» и студии <a href="https://www.instagram.com/k0shta/" target="_blank">koshta</a>',
                        'special: digital-analog', 'knife-theme'
                    )
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'units');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/special/digital-analog/footer');
