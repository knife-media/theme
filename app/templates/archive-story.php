<?php
/**
 * Story archive template
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.7
 */
get_header(); ?>

<div class="block-wrapper">
    <div class="widget-story">
        <?php
            while(have_posts()) : the_post();

                get_template_part('templates/widget', 'story');

            endwhile;
        ?>
    </div>
</div>

<?php if(get_next_posts_link()) : ?>
    <nav class="block-navigate">
        <?php
            next_posts_link(__('Больше историй', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
