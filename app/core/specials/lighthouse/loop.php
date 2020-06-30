<?php
/**
 * Lighthouse: loop template
 *
 * @package knife-theme
 * @since 1.10
 */
?>

<div class="unit unit--triple">
    <div class="unit__inner">
        <div class="unit__image">
            <?php
                the_post_thumbnail('double',
                    ['class' => 'unit__image-thumbnail', 'loading' => 'lazy']
                );
            ?>
        </div>

        <div class="unit__content">
            <?php
                printf(
                    '<a class="unit__content-link" href="%1$s"><span>%2$s</span></a>',
                    esc_url(get_permalink()),
                    get_post_meta(get_the_ID(), 'post-hero', true)
                );

                printf(
                    '<p class="unit__content-info">%s</p>',
                    get_post_meta(get_the_ID(), 'post-info', true)
                );
            ?>
        </div>
    </div>
</div>
