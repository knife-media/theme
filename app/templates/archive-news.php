<?php
/**
 * News loop template without sidebar
 *
 * @package knife-theme
 * @since 1.3
 * @version 1.17
 */

get_header(); ?>

<div class="widget-news">
    <?php
    while ( have_posts() ) :
        the_post();

        get_template_part( 'partials/loop', 'news' );
    endwhile;
    ?>
</div>

<?php if ( have_posts() ) : ?>
    <nav class="navigate">
        <?php
        next_posts_link( esc_html__( 'Следующие', 'knife-theme' ) );
        ?>
    </nav>
<?php endif; ?>

<?php
get_footer();
