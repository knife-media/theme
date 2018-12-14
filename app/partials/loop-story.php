<?php
/**
 * Story archive loop partial template
 *
 * @package knife-theme
 * @since 1.4
 */
?>

<div class="story">
    <div class="story__inner">
       <div class="story__image">
            <?php
                the_post_thumbnail('triple', ['class' => 'story__image-thumbnail']);
            ?>
        </div>

        <div class="story__content">
            <?php
                printf(
                    '<a class="story__content-link" href="%1$s">%2$s</a>',
                    esc_url(get_permalink()),
                    get_the_title()
                );
            ?>
        </div>
    </div>
</div>
