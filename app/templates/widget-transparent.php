<?php
/**
 * Recent widget template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="widget-transparent__inner">
    <?php
        if(!empty($emoji)) :
            printf(
                '<span class="widget-transparent__emoji">%s</span>',
                wp_encode_emoji($emoji)
            );
        endif;
    ?>

    <div class="widget-transparent__content">
        <?php
            the_info(
                '<div class="widget-transparent__content-info info">', '</div>',
                ['author', 'date']
            );

            printf(
                '<a class="widget-transparent__content-link" href="%2$s">%1$s</a>',
                get_the_title(), esc_url(get_permalink())
            );
        ?>
    </div>
</div>
