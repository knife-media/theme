<?php
/**
 * Template for showing site front-page
 *
 * Difference between front-page and home.php on the link below
 *
 * @link https://wordpress.stackexchange.com/a/110987
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.10
 */

get_header(); ?>

<?php
    if(is_active_sidebar('knife-feature')) :
        dynamic_sidebar('knife-feature');
    endif;
?>

<?php if(is_active_sidebar('knife-frontal')) : ?>
    <?php
        dynamic_sidebar('knife-frontal');
    ?>

    <nav class="navigate">
        <?php
            printf('<a class="button" href="%2$s">%1$s</a>',
                __('Все статьи', 'knife-theme'),
                esc_url(home_url('/longreads/page/2'))
            );
        ?>
    </nav>
<?php endif; ?>

<?php get_footer();
