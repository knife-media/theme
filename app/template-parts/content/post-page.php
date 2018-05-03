<?php
/**
 * Content template using basically for pages
 *
 * @package knife-theme
 * @since 1.1
 */
?>

<article <?php post_class('post post--page'); ?> id="post-<?php the_ID(); ?>">

    <div class="post__content custom">
        <?php the_content(); ?>
    </div>

</article>
