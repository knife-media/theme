<div id="knife-promo-box">
    <?php
        $post_id = get_the_ID();

        // Check if post promoted
        $promo = get_post_meta($post_id, self::$meta_promo, true);

        // Get post teaser
        $teaser = get_post_meta($post_id, self::$meta_teaser, true);

        // Get post pixel
        $pixel = get_post_meta($post_id, self::$meta_pixel, true);

        // Get post ORD
        $ord = get_post_meta($post_id, self::$meta_ord, true);

        // Get promo options
        $options = get_post_meta($post_id, self::$meta_options, true);

        printf(
            '<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
            esc_attr(self::$meta_promo),
            __('Партнерский материал', 'knife-theme'),
            checked($promo, 1, false)
        );
    ?>

    <div class="promo-advert hidden">
        <?php
            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[link]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                esc_url($options['link'] ?? ''),
                __('Ссылка с плашки:', 'knife-theme')
            );

            printf(
                '<p><strong>%2$s</strong><input class="logo widefat" type="text" name="%1$s[logo]" value="%3$s">%4$s</p>',
                esc_attr(self::$meta_options),
                __('Ссылка на логотип:', 'knife-theme'),
                esc_url($options['logo'] ?? ''),
                sprintf(
                    '<small><a class="upload" href="%s" target="_blank">%s</a></small>',
                    esc_url(admin_url('/media-new.php')),
                    __('Загрузить изображение', 'knife-theme')
                )
            );

            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[title]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                sanitize_text_field($options['title'] ?? ''),
                __('Рекламодатель:', 'knife-theme')
            );

            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[text]" value="%2$s"></p>',
                esc_attr(self::$meta_options),
                sanitize_text_field($options['text'] ?? __('Партнерский материал', 'knife-theme')),
                __('Тип материала:', 'knife-theme')
            );
        ?>

        <div>
            <?php
                printf(
                    '<strong>%s</strong>',
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

    <div class="promo-teaser">
        <?php
            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
                esc_attr(self::$meta_teaser),
                esc_attr($teaser),
                __('Пользовательская ссылка:', 'knife-theme')
            );
        ?>
    </div>

    <div class="promo-pixel">
        <?php
            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
                esc_attr(self::$meta_pixel),
                esc_attr($pixel),
                __('Пиксель для подсчета охвата:', 'knife-theme')
            );
        ?>

        <?php
            printf(
                '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
                esc_attr(self::$meta_ord),
                esc_attr($ord),
                __('Пиксель для ОРД:', 'knife-theme')
            );
        ?>
    </div>
</div>
