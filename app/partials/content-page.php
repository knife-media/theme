<?php
/**
 * Content template using basically for pages
 *
 * @package knife-theme
 * @since 1.1
 * @version 1.4
 */
?>

<article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
    <div class="entry-content">
        <?php
            the_content();
        ?>
    </div>
</article>
