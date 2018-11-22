<div id="knife-generator-box">
    <?php
        $post_id = get_the_ID();

        // Generator options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Generator catalog items
        $catalog = get_post_meta($post_id, self::$meta_catalog);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">
        <div class="manage manage--general">
            <div class="manage__option">
                <strong><?php _e('Текст на кнопке', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[button_text]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['button_text']) ?? __('Сгенерировать', 'knife-theme')
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
                            esc_attr($options['button_repeat']) ?? __('Попробовать другой', 'knife-theme')
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
                        printf('<input type="hidden" name="%s[page_background]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['page_background']) ?? '#ffffff'
                        );
                    ?>
                </p>
            </div>

            <div class="manage__color">
                <strong><?php _e('Шрифт страницы', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="hidden" name="%s[page_color]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['page_color']) ?? '#000000'
                        );
                    ?>
                </p>
            </div>


            <div class="manage__color">
                <strong><?php _e('Цвет кнопки', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="hidden" name="%s[button_background]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['button_background']) ?? ''
                        );
                    ?>
                </p>
            </div>

            <div class="manage__color">
                <strong><?php _e('Шрифт кнопки', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="hidden" name="%s[button_color]" value="%s">',
                            esc_attr(self::$meta_options),
                            esc_attr($options['button_color'] ?? '')
                        );
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="box box--catalog">
    <?php foreach($catalog as $i => $item) : ?>
        <div class="item">
            <div class="item__poster">
                <figure class="item__poster-figure">
                    <?php if(!empty($item['poster'])) : ?>
                        <img class="item__poster-image" src="<?php echo $item['poster']; ?>" alt="">
                    <?php endif; ?>

                    <figcaption class="item__poster-blank">
                        <?php _e('Выбрать изображение для постера', 'knife-theme'); ?>
                    </figcaption>

                    <?php
                        printf('<input class="item__poster-media" type="hidden" name="%s[][poster]" value="%s">',
                            esc_attr(self::$meta_catalog),
                            esc_attr($item['poster'] ?? '')
                        );
                    ?>
                </figure>

                <button class="item__poster-generate button" type="button"><?php _e('Сгенерировать', 'knife-theme'); ?></button>
                <button class="item__poster-settings button" type="button" disabled ><?php _e('Настройки', 'knife-theme'); ?></button>

                <span class="dashicons dashicons-trash"></span>
            </div>

            <div class="item__option">
                <strong><?php _e('Заголовок', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<input type="text" name="%s[][title]" value="%s">',
                            esc_attr(self::$meta_catalog),
                            esc_attr($item['title'] ?? '')
                        );
                    ?>
                </p>
            </div>

            <div class="item__option">
                <strong><?php _e('Описание', 'knife-theme'); ?></strong>

                <p>
                    <?php
                        printf('<textarea name="%s[][description]">%s</textarea>',
                            esc_attr(self::$meta_catalog),
                            esc_attr($item['description'] ?? '')
                        );
                    ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="box box--actions">
        <button class="actions__add button"><?php _e('Добавить элемент', 'knife-theme'); ?></button>
    </div>
</div>
