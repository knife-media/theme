<?php
/**
 * Ask widget template
 *
 * @package knife-theme
 * @since 1.7
 */
?>

<div class="widget-ask__inner">
    <?php
        the_info(
            '<div class="widget-ask__info info">', '</div>',
            ['question', 'date']
        );

        printf(
            '<a class="widget-ask__link" href="%1$s">%2$s</a>',
            esc_url(get_permalink()),
            get_the_title()
        );
    ?>

    <div class="widget-ask__author">
        <?php
            printf(
                '<span class="widget-ask__author-label">%s</span>',
                __('Отвечает', 'knife-theme')
            );

            the_info(
                null, null, ['emoji', 'author']
            );
        ?>
    </div>
</div>
