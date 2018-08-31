<?php
/**
 * Template for showing site front-page
 *
 * Difference between front-page and home.php in a link below
 *
 * @link https://wordpress.stackexchange.com/a/110987
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

    <?php if(is_active_sidebar('knife-under-header')) : ?>
        <div class="">
            <?php dynamic_sidebar('knife-under-header'); ?>
        </div>
    <?php endif; ?>


    <?php if(is_active_sidebar('knife-feature-stripe')) : ?>
        <div class="content">
            <div class="split split--content">
                <?php dynamic_sidebar('knife-feature-stripe'); ?>
            </div>

        <?php if(is_active_sidebar('knife-feature-sidebar')) : ?>
            <aside class="split split--sidebar">
                <?php dynamic_sidebar('knife-feature-sidebar'); ?>
            </aside>
        <?php endif; ?>
        </div>
    <?php endif; ?>


    <?php if(is_active_sidebar('knife-frontal')) : ?>
        <div class="content content--press">
            <?php
                dynamic_sidebar('knife-frontal');
            ?>
        </div>

        <nav class="navigation">
            <?php
                printf('<a class="button" href="%2$s">%1$s</a>',
                    __('Больше статей', 'knife-theme'),
                    esc_url(home_url('/recent/page/4/'))
                );
            ?>
        </nav>
    <?php endif; ?>
</main>

<?php get_footer();
