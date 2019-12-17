<?php
/**
 * Story widget template
 *
 * @package knife-theme
 * @since 1.4
 * @version 1.11
 */
?>

<div class="widget-story__wrapper">

    <?php while($query->have_posts()) : $query->the_post(); ?>
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
    <?php endwhile; ?>

    <?php wp_reset_query(); ?>
</div>
