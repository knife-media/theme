<div id="knife-snippet-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Get post background meta
        $snippet = get_post_meta($post_id, self::$post_meta, true);
    ?>

    <figure class="snippet__poster">
        <?php
            if($snippet) {
                printf('<img src="%s" alt="">', esc_url($snippet));
            }

            printf(
                '<figcaption class="snippet__poster-caption">%s</figcaption>',
                __('Выбрать изображение для постера', 'knife-theme')
            );

            printf(
                '<input class="snippet__poster-attachment" type="hidden" name="%s" value="%s">',
                esc_attr(self::$post_meta),
                sanitize_text_field($snippet)
            );
        ?>
    </figure>

    <div class="snippet__footer">
        <?php
            if(class_exists('Knife_Poster_Templates')) {
                printf(
                    '<button class="snippet__footer-generate button" type="button">%s</button>',
                    __('Сгенерировать', 'knife-theme')
                );
            }
        ?>

        <span class="snippet__footer-spinner spinner"></span>
    </div>

    <div class="snippet__warning"></div>
</div>

<div class="hide-if-js">
    <p><?php _e('Эта функция требует JavaScript', 'knife-theme'); ?></p>
</div>
