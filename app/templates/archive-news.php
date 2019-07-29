<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.10
 */

get_header(); ?>

<div class="block-wrapper">
   <div class="widget-news">
       <?php
            while(have_posts()) : the_post();

                get_template_part('templates/widget', 'news');

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
