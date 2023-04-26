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
 * @version 1.17
 */

get_header(); ?>

<?php
if ( is_active_sidebar( 'knife-feature' ) ) :
    dynamic_sidebar( 'knife-feature' );
endif;

if ( is_active_sidebar( 'knife-frontal' ) ) :
    dynamic_sidebar( 'knife-frontal' );
endif;
?>

<?php if ( is_active_sidebar( 'knife-frontal' ) ) : ?>
    <nav class="navigate">
        <?php
            printf(
                '<a class="button" href="%s/page/2/">%s</a>',
                esc_url( untrailingslashit( get_category_link( get_category_by_slug( 'longreads' ) ) ) ),
                esc_html__( 'Все статьи', 'knife-theme' )
            );
        ?>
    </nav>
<?php endif; ?>

<?php
get_footer();
