<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
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

<?php if(have_posts() && get_next_posts_link()) : ?>
    <nav class="block-navigate">
        <?php
            next_posts_link(__('Больше новостей', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
