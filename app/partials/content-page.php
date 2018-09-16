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
        <div class="entry-content__inner article">
            <?php
                the_content();
            ?>
        </div>
    </div>
</article>
