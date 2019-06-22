<div id="knife-generator-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Generator options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Generator items
        $items = get_post_meta($post_id, self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, []);

        wp_nonce_field('metabox', self::$metabox_nonce);
    ?>

    <div class="box box--manage">
        <div class="manage manage--general">
            <p class="manage__title">
                <?php _e('Настройки генератора', 'knife-theme'); ?>
            </p>

            <p class="manage__check">
                <?php
                    printf('<label><input data-manage="blank" type="checkbox" name="%s[blank]" value="1"%s>%s</label>',
                        esc_attr(self::$meta_options),
                        checked(isset($options['blank']), true, false),
                        __('Не использовать изображения', 'knife-theme')
                    );
                ?>
            </p>

            <p class="manage__check">
                <?php
                    printf('<label><input data-manage="noshare" type="checkbox" name="%s[noshare]" value="1"%s>%s</label>',
                        esc_attr(self::$meta_options),
                        checked(isset($options['noshare']), true, false),
                        __('Шарить страницу генератора, вместо результата', 'knife-theme')
                    );
                ?>
            </p>


            <p class="manage__input">
                <strong><?php _e('Текст на кнопке', 'knife-theme'); ?></strong>

                <?php
                    printf('<input type="text" name="%s[button_text]" value="%s">',
                        esc_attr(self::$meta_options),
                        esc_attr($options['button_text'] ?? __('Сгенерировать', 'knife-theme'))
                    );
                ?>
            </p>

            <p class="manage__input">
                <strong><?php _e('Текст на кнопке повтора', 'knife-theme'); ?></strong>

                <?php
                    printf('<input type="text" name="%s[button_repeat]" value="%s">',
                        esc_attr(self::$meta_options),
                        esc_attr($options['button_repeat'] ?? __('Попробовать другой', 'knife-theme'))
                    );
                ?>
            </p>
        </div>

        <div class="manage manage--colors">
            <div class="manage__color">
                <strong><?php _e('Фон страницы', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[page_background]" value="%s">',
                            esc_attr(self::$meta_options),
                            sanitize_hex_color($options['page_background'] ?? '#ffffff')
                        );
                    ?>
                </p>
            </div>

            <div class="manage__color">
                <strong><?php _e('Шрифт страницы', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[page_color]" value="%s">',
                            esc_attr(self::$meta_options),
                            sanitize_hex_color($options['page_color'] ?? '#000000')
                        );
                    ?>
                </p>
            </div>

            <div class="manage__color">
                <strong><?php _e('Цвет кнопки', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[button_background]" value="%s">',
                            esc_attr(self::$meta_options),
                            sanitize_hex_color($options['button_background'] ?? '')
                        );
                    ?>
                </p>
            </div>

            <div class="manage__color">
                <strong><?php _e('Шрифт кнопки', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[button_color]" value="%s">',
                            esc_attr(self::$meta_options),
                            sanitize_hex_color($options['button_color'] ?? '')
                        );
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item">
                <div class="item__text">
                    <?php
                        printf(
                            '<p class="item__text-heading"><strong>%2$s</strong><input type="text" data-item="heading" value="%1$s"></p>',
                            sanitize_text_field($item['heading'] ?? ''),
                            __('Заголовок', 'knife-theme')
                        );

                        printf(
                            '<p class="item__text-description"><strong>%2$s</strong><textarea data-item="description">%1$s</textarea></p>',
                            wp_kses_post($item['description'] ?? ''),
                            __('Описание', 'knife-theme')
                        );
                    ?>
                </div>

                <div class="item__image">
                    <figure class="item__image-poster">
                        <?php
                            if(!empty($item['poster'])) {
                                printf('<img src="%s" alt="">', esc_url($item['poster']));
                            }
                        ?>

                        <figcaption class="item__image-caption">+</figcaption>

                        <?php
                            printf('<input type="hidden" data-item="poster" value="%s">',
                                sanitize_text_field($item['poster'] ?? '')
                            );

                            printf('<input type="hidden" data-item="attachment" value="%s">',
                                sanitize_text_field($item['attachment'] ?? '')
                            );
                        ?>
                    </figure>

                    <div class="item__image-footer">
                        <?php
                            if(method_exists('Knife_Poster_Templates', 'print_select')) {
                                Knife_Poster_Templates::print_select([
                                    'attributes' => [
                                        'class' => 'item__image-template',
                                        'data-item' => 'template'
                                    ],
                                    'selected' => esc_attr($item['template'] ?? '')
                                ]);

                                printf(
                                    '<button class="item__image-generate button" type="button">%s</button>',
                                    __('Сгенерировать', 'knife-theme')
                                );
                            }
                        ?>

                        <span class="item__image-spinner spinner"></span>
                    </div>

                    <div class="item__image-warning"></div>
                </div>

                <span class="item__delete dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="actions__add button" type="button">%s</button>',
                __('Добавить элемент', 'knife-theme')
            );
        ?>
    </div>
</div>
