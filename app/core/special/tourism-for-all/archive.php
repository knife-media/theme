<?php
/**
 * special archive template
 *
 * @package knife-theme
 * @since 1.15
 */

get_header(); ?>

<div class="content">

    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<h1>%s</h1>',
                    _x('Путешествовать с инвалидностью можно. Кто в России делает туризм доступным', 'special: tourism-for-all', 'knife-theme')
                );
            ?>
        </div>
    </div>

    <div class="archive">
        <?php
            while(have_posts()) : the_post();

                get_template_part('core/special/tourism-for-all/loop');

            endwhile;
        ?>
    </div>
</div>

<?php get_template_part('core/special/tourism-for-all/footer');
