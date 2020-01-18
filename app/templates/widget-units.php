<?php
/**
 * Unit widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<div class="unit unit--<?php echo esc_attr($size) ?>">
    <div class="unit__inner">
        <?php
            the_info(
                '<div class="unit__head">', '</div>',
                ['head']
            );
        ?>

        <div class="unit__image">
            <?php
                the_post_thumbnail($size, ['class' => 'unit__image-thumbnail', 'loading' => 'lazy']);
            ?>
        </div>

        <div class="unit__content">
            <?php
                printf(
                    '<a class="unit__content-link" href="%1$s">%2$s</a>',
                    esc_url(get_permalink()),
                    get_the_title()
                );

                the_info(
                    '<div class="unit__content-info">', '</div>',
                    ['author', 'date', 'best']
                );
            ?>
        </div>
    </div>
</div>
