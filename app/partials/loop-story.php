<?php
/**
 * Story loop template
 *
 * @package knife-theme
 * @since 1.11
 */
?>

<div class="widget-story__inner">
    <div class="widget-story__content">

        <div class="widget-story__image">
            <?php
                the_post_thumbnail('triple', ['class' => 'widget-story__image-thumbnail']);
            ?>
        </div>

        <div class="widget-story__title">
            <?php
                printf(
                    '<a class="widget-story__title-link" href="%2$s">%1$s</a>',
                    get_the_title(),
                    esc_url(get_permalink())
                );
            ?>
        </div>
    </div>
</div>
