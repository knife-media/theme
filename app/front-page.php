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
 * @version 1.9
 */

get_header(); ?>

<?php if(is_active_sidebar('knife-feature')) : ?>
    <div class="block-wrapper">
        <?php
            dynamic_sidebar('knife-feature');
        ?>
    </div>
<?php endif; ?>

<?php if(is_active_sidebar('knife-frontal')) : ?>
    <div class="block-wrapper">
        <?php
            dynamic_sidebar('knife-frontal');
        ?>
    </div>

    <nav class="block-navigate">
        <?php
            printf('<a class="button" href="%2$s">%1$s</a>',
                __('Больше статей', 'knife-theme'),
                esc_url(home_url('/recent/'))
            );
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
