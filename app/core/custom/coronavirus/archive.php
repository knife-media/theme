<?php
/**
 * coronavirus: archive template
 *
 * @package knife-theme
 * @since 1.12
 */

get_header(); ?>

<?php if(have_posts() && get_the_archive_title()) : ?>
    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<h1>%s</h1>',
                    _x('Коронавирус', 'custom: coronavirus', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x('В этом разделе мы собираем все материалы о пандемии: как не заболеть, чем заняться дома, какого экономического эффекта ждать от пандемии и как правильно оценивать ситуацию', 'special: coronavirus', 'knife-theme')
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

<?php if(have_posts()) : ?>
    <nav class="navigate">
        <?php
            previous_posts_link(__('Предыдущие', 'knife-theme'));
            next_posts_link(__('Следующие', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
