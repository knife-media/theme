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

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item">
                <div class="option option--general">
                    <?php
                        printf(
                            '<p class="option__general"><strong>%3$s</strong><input type="text" data-item="heading" name="%1$s[][heading]" value="%2$s"></p>',
                            esc_attr(self::$meta_items),
                            esc_attr($item['heading'] ?? ''),
                            __('Заголовок', 'knife-theme')
                        );

                        printf(
                            '<p class="option__general"><strong>%3$s</strong><textarea data-item="description" name="%1$s[][description]">%2$s</textarea></p>',
                            esc_attr(self::$meta_items),
                            esc_attr($item['description'] ?? ''),
                            __('Описание', 'knife-theme')
                        );
                    ?>
                </div>

                <div class="option option--relative">
                    <div class="option__relative">
                        <figure class="option__relative-poster">
                            <?php
                                if(!empty($item['poster'])) {
                                    printf('<img class="option__relative-image" src="%s" alt="">',
                                        esc_url($item['poster'])
                                    );
                                }

                                printf(
                                    '<figcaption class="option__relative-caption">%s</figcaption>',
                                    __('Выбрать изображение для постера', 'knife-theme')
                                );

                                printf('<input type="hidden" data-item="poster" name="%s[][poster]" value="%s">',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['poster'] ?? '')
                                );

                                printf('<input type="hidden" data-item="attachment" name="%s[][attachment]" value="%s">',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['attachment'] ?? '')
                                );
                            ?>
                        </figure>

                        <div class="option__relative-footer">
                            <?php
                                if(method_exists('Knife_Poster_Templates', 'get_select')) {
                                    Knife_Poster_Templates::get_select([
                                        'attributes' => [
                                            'class' => 'option__relative-template',
                                            'data-item' => 'template',
                                            'name' => esc_attr(self::$meta_items) . '[][template]'
                                        ],
                                        'selected' => esc_attr($item['template'] ?? '')
                                    ]);

                                    printf('<button class="option__relative-generate button" type="button">%s</button>',
                                        __('Сгенерировать', 'knife-theme')
                                    );
                                }

                                printf('<button class="option__relative-settings button" disabled type="button">%s</button>',
                                    __('Настройки', 'knife-theme')
                                );
                            ?>

                            <span class="option__relative-spinner spinner"></span>
                            <span class="dashicons dashicons-trash"></span>
                        </div>

                        <div class="option__relative-warning"></div>
                    </div>
                </div>
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

    <div class="box box--manage">
        <div class="manage manage--general">
            <div class="manage__option">
                <strong><?php _e('Текст на кнопке', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[button_text]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['button_text'] ?? __('Сгенерировать', 'knife-theme'))
                        );
                    ?>
                </p>
            </div>

            <div class="manage__option">
                <strong><?php _e('Текст на кнопке повтора', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[button_repeat]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['button_repeat'] ?? __('Попробовать другой', 'knife-theme'))
                        );
                    ?>
                </p>
            </div>
        </div>

        <div class="manage manage--colors">
            <div class="manage__color">
                <strong><?php _e('Фон страницы', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[page_background]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['page_background'] ?? '#ffffff')
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
                            esc_attr($options['page_color'] ?? '#000000')
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
                            esc_attr($options['button_background'] ?? '')
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
                            esc_attr($options['button_color'] ?? '')
                        );
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
