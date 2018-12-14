<?php
/**
 * Select archive template
 *
 * @package knife-theme
 * @since 1.5
 * @version 1.6
 */
get_header(); ?>

<div class="block-wrapper">
    <div class="select">
        <div class="select__head head">
            <span><?php _e('Все подборки', 'knife-theme'); ?></span>
        </div>

        <?php
            while(have_posts()) : the_post();
                printf(
                    '<a class="select__link" href="%1$s">%2$s</a>',
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
