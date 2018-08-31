<?php
/**
 * Story content template
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.4
 */
get_header(); ?>

<div class="wrap">
    <section class="glide">
        <?php
            while(have_posts()) : the_post();

                get_template_part('partials/content', 'story');

            endwhile;
        ?>
    </section>

    <section class="content">
        <?php
            $stories = new WP_Query([
                'post_type' => 'story',
                'post__not_in' => [$post->ID],
                'posts_per_page' => 4
            ]);

            if($stories->have_posts()) :
                while($stories->have_posts()) : $stories->the_post();

                    get_template_part('partials/loop', 'story');

                endwhile;

                wp_reset_query();
            endif;
        ?>
    </section>

    <nav class="navigation">
        <?php
            printf('<a class="button" href="%2$s">%1$s</a>',
                __('Все истории', 'knife-theme'),
                esc_url(get_post_type_archive_link('story'))
            );
        ?>
    </nav>
</div>

<?php get_footer();
