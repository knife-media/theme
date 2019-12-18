<div id="knife-snippet-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Get post snippet image meta
        $snippet = get_post_meta($post_id, self::$meta_image, true);

        // Get snippet options
        $options = (array) get_post_meta($post_id, self::$meta_options, true);

        if(empty($options['text'])) {
            $options['text'] = get_the_title();
        }
    ?>

    <div class="snippet">
        <figure class="snippet__poster">
            <?php
                if($snippet) {
                    printf('<img src="%s" alt="">', esc_url($snippet));
                }

                printf(
                    '<input class="snippet__poster-image" type="hidden" name="%s" value="%s">',
                    esc_attr(self::$meta_image),
                    sanitize_text_field($snippet)
                );

                printf(
                    '<input class="snippet__poster-attachment" type="hidden" name="%s[attachment]" value="%s">',
                    esc_attr(self::$meta_options),
                    absint($options['attachment'] ?? 0)
                );
            ?>

            <figcaption class="snippet__poster-caption">+</figcaption>
            <span class="snippet__poster-preview dashicons dashicons-search"></span>
        </figure>

        <div class="snippet__title">
            <?php
                printf(
                    '<textarea class="snippet__title-textarea" name="%s[text]" placeholder="%s">%s</textarea>',
                    esc_attr(self::$meta_options),
                    __('Текст на обложке', 'knife-theme'),
                    sanitize_textarea_field($options['text'])
                )
            ?>
        </div>

        <div class="snippet__footer">
            <?php
                if(class_exists('Knife_Poster_Templates')) {
                    printf(
                        '<button class="snippet__footer-generate button" type="button">%s</button>',
                        __('Сгенерировать', 'knife-theme')
                    );
                }

                printf(
                    '<button class="snippet__footer-delete button" type="button">%s</button>',
                    __('Удалить', 'knife-theme')
                );
            ?>

            <span class="snippet__footer-spinner spinner"></span>
        </div>

        <div class="snippet__warning"></div>
    </div>
</div>

<div class="hide-if-js">
    <p><?php _e('Эта функция требует JavaScript', 'knife-theme'); ?></p>
</div>
