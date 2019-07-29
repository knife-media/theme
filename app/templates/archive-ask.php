<?php
/**
 * Ask archive template
 *
 * @package knife-theme
 * @since 1.7
 * @version 1.10
 */
get_header(); ?>

<div class="block-wrapper">
    <div class="widget-ask">
        <?php
            printf('<span class="widget-ask__head head">%s</span>',
                __('Вопросы', 'knife-theme')
            );

            while(have_posts()) : the_post();

                get_template_part('templates/widget', 'ask');

            endwhile;
        ?>
    </div>
</div>

<?php if(have_posts()) : ?>
    <nav class="block-navigate">
        <?php
            previous_posts_link(__('Предыдущие', 'knife-theme'));
            next_posts_link(__('Следующие', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
