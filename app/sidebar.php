<?php
/**
 * Single template sidebar
 *
 * Show last news and related posts
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.5
 */
?>

<aside class="sidebar">
    <?php if(is_active_sidebar('knife-sidebar')) : ?>
        <div class="sidebar__widgets">
            <?php
                dynamic_sidebar('knife-sidebar');
            ?>
        </div>
    <?php endif; ?>

    <?php if(is_singular('post')) : ?>
        <div class="sidebar__related">
            <?php
                $related = new WP_Query([
                    'post_type' => 'post',
                    'post__not_in' => [$post->ID],
                    'posts_per_page' => 2,
                    'ignore_sticky_posts' => 1,
                    'query_type' => 'related'
                ]);

                if($related->have_posts()) :
                    while($related->have_posts()) : $related->the_post();

                        get_template_part('partials/loop', 'sidebar');

                    endwhile;

                    wp_reset_query();
                endif;
            ?>
        </div>
    <?php endif; ?>
</aside>
