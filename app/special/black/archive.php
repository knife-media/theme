<?php
/**
 * Black: archive template
 *
 * @package knife-theme
 * @since 1.13
 */

get_header(); ?>

<?php if(have_posts() && get_the_archive_title()) : ?>
    <div class="caption">
        <div class="caption__description">
            <?php
                printf(
                    '<h1>%s</h1>',
                    _x('Черный нож', 'special: black', 'knife-theme')
                );

                printf(
                    '<p>%s</p>',
                    _x('«Черный нож» — тайный журнал для избранных, вестник параллельного мира, в котором хочется жить. Если вы читаете, то вы не умрете никогда.', 'special: black', 'knife-theme')
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

<?php get_template_part('special/black/footer');
