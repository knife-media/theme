<?php
/**
 * Template for display single post
 *
 * @package knife-theme
 * @since 1.1
 */

get_header(); ?>

<main class="wrap">

<?php if(has_term('vdnh', 'special')) : ?>
    <a class="caption special" href="/special/vdnh">
        <img src="https://knife.media/wp-content/uploads/2018/06/vdnh-logo.png" style="width: 50px; height: 50px; margin-right: 1rem;">
        <h1><?php _e('ВДНХ &mdash; место силы','knife-theme'); ?></h1>
    </a>
<?php endif; ?>


    <div class="content block">
<?php
    if (have_posts()) :

        while (have_posts()) : the_post();

            // Include specific content template
            get_template_part('template-parts/content/post', get_post_format());

        endwhile;

    else:

        // Include "no posts found" template
        get_template_part('template-parts/content/post', 'none');

    endif;
?>
    </div>

</main>


<?php
if(has_term('vdnh', 'special'))
    get_footer('vdnh');
else
    get_footer();
