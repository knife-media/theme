<?php
/**
 * Ask archive template
 *
 * @package knife-theme
 * @since 1.7
 */
get_header(); ?>

<div class="block-wrapper">
    <div class="widget-ask">
        <?php
            printf('<span class="widget-ask__head head">%s</span>',
                __('Вопросы', 'knife-theme')
            );

            while(have_posts()) : the_post();

                get_template_part('partials/loop', 'ask');

            endwhile;
        ?>
    </div>
</div>

<?php if(get_next_posts_link()) : ?>
    <nav class="block-navigate">
        <?php
            next_posts_link(__('Больше вопросов', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
