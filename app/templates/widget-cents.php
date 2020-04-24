<?php
/**
 * Cents widget template
 *
 * @package knife-theme
 * @since 1.12
 */
?>

<div class="widget-cents__wrapper">
    <?php
        printf(
            '<a class="widget-cents__title" href="%s">%s</a>',
            esc_url(get_permalink($instance['page'])),
            esc_html($instance['title'])
        );
    ?>

    <?php foreach($instance['posts'] as $i => $post) : ?>
        <div class="widget-cents__inner">
            <?php
                printf(
                    '<a class="widget-cents__link" href="%2$s#%3$d" data-before="#%3$d">%1$s</a>',
                    esc_html($post['title']),
                    esc_url(get_permalink($instance['page'])),
                    $instance['count'] - $i
                );

                printf(
                    '<div class="widget-cents__content" data-folded="%2$s">%1$s</div>',
                    wpautop(esc_html($post['content'])),
                    __('Читать дальше', 'knife-theme')
                );
            ?>
        </div>
    <?php endforeach; ?>
</div>
