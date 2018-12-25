<div id="knife-generator-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Generator options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Generator items
        $items = get_post_meta($post_id, self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, ['caption' => '', 'description' => '']);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item <?php echo ($i === 0) ? 'item--hidden' : ''; ?>">
                <div class="option option--general">
                    <div class="option__general">
                        <strong><?php _e('Заголовок', 'knife-theme'); ?></strong>

                        <p>
                            <?php
                                printf('<input class="option__general-caption" type="text" name="%s[][caption]" value="%s">',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['caption'] ?? '')
                                );
                            ?>
                        </p>
                    </div>

                    <div class="option__general">
                        <strong><?php _e('Описание', 'knife-theme'); ?></strong>

                        <p>
                            <?php
                                printf('<textarea class="option__general-description" name="%s[][description]">%s</textarea>',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['description'] ?? '')
                                );
                            ?>
                        </p>
                    </div>
                </div>

                <div class="option option--relative">
                    <div class="option__relative">
                        <figure class="option__relative-poster">
                            <?php if(!empty($item['poster'])) : ?>
                                <img class="option__relative-image" src="<?php echo $item['poster']; ?>" alt="">
                            <?php endif; ?>

                            <figcaption class="option__relative-blank">
                                <?php _e('Выбрать изображение для постера', 'knife-theme'); ?>
                            </figcaption>

                            <?php
                                printf('<input class="option__relative-media" type="hidden" name="%s[][poster]" value="%s">',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['poster'] ?? '')
                                );

                                printf('<input class="option__relative-attachment" type="hidden" name="%s[][attachment]" value="%s">',
                                    esc_attr(self::$meta_items),
                                    esc_attr($item['attachment'] ?? '')
                                );
                            ?>
                        </figure>

                        <p class="option__relative-message">Ошибка генерации: No font file set!</p>

                        <?php
                            printf('<button class="option__relative-generate button" type="button">%s</button>',
                                __('Сгенерировать', 'knife-theme')
                            );

                            printf('<button class="option__relative-settings button" disabled type="button">%s</button>',
                                __('Настройки', 'knife-theme')
                            );
                        ?>

                        <span class="option__relative-spinner spinner"></span>
                        <span class="dashicons dashicons-trash"></span>
                    </div>
                </div>

                <div class="option option--advanced">
                    <div class="option__layers">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="actions__add button">%s</button>',
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
