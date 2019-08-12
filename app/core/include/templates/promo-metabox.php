<div id="knife-promo-box">
    <?php
        $post_id = get_the_ID();

        // Check if post promoted
        $promo = get_post_meta($post_id, self::$meta_promo, true);

        // Get promo options
        $options = get_post_meta($post_id, self::$meta_options, true);

        printf(
            '<p style="margin-bottom: 0;"><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
            esc_attr(self::$meta_promo),
            __('Партнерский материал', 'knife-theme'),
            checked($promo, 1, false)
        );

        wp_nonce_field('metabox', self::$metabox_nonce);
    ?>

    <div class="promo hidden">
        <?php
            printf(
                '<p><label>%3$s</label><input class="widefat" type="text" name="%1$s[link]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                esc_url($options['link'] ?? ''),
                __('Ссылка с плашки:', 'knife-theme')
            );

            printf(
                '<p><label>%2$s</label><input class="widefat" type="text" name="%1$s[logo]" value="%3$s">%4$s</p>',
                esc_attr(self::$meta_options),
                __('Ссылка на логотип:', 'knife-theme'),
                esc_url($options['logo'] ?? ''),
                sprintf(
                    '<small><a href="%s" target="_blank">%s</a></small>',
                    esc_url(admin_url('/media-new.php')),
                    __('Загрузить изображение', 'knife-theme')
                )
            );

            printf(
                '<p><label>%3$s</label><input class="widefat" type="text" name="%1$s[title]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                sanitize_text_field($options['title'] ?? ''),
                __('Рекламодатель:', 'knife-theme')
            );

            printf(
                '<p><label>%3$s</label><input class="widefat" type="text" name="%1$s[text]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                sanitize_text_field($options['text'] ?? __('Партнерский материал', 'knife-theme')),
                __('Тип материала:', 'knife-theme')
            );
        ?>

        <div>
            <?php
                printf(
                    '<label style="display: block; margin-bottom: 5px;">%s</label>',
                    __('Цвет плашки:', 'knife-theme')
                );

                printf(
                    '<input class="color-picker" name="%s[color]" type="text" value="%s">',
                    esc_attr(self::$meta_options),
                    sanitize_text_field($options['color'] ?? ''),
                );
            ?>
        </div>
    </div>
</div>
