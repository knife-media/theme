<?php
/**
 * Select archive template
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.7
 */
get_header(); ?>

<div class="block-wrapper">
    <div class="widget-select">
        <?php
            printf('<span class="widget-select__head head">%s</span>',
                __('Все подборки', 'knife-theme')
            );

            while(have_posts()) : the_post();
                printf(
                    '<a class="widget-select__link" href="%1$s">%2$s</a>',
                    esc_url(get_permalink()),
                    get_the_title()
                );
            endwhile;
        ?>
    </div>
</div>

<?php if(get_next_posts_link()) : ?>
    <nav class="block-navigate">
        <?php
            next_posts_link(__('Больше подборок', 'knife-theme'));
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
