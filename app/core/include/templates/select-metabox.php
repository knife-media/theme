<div id="knife-select-box" data-action="<?php echo esc_attr(self::$action); ?>" data-nonce="<?php echo wp_create_nonce(self::$nonce); ?>">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, ['text' => '', 'link' => '']);
    ?>

    <div class="box box--manage">
        <div class="option">
            <label>
                <strong><?php _e('Ссылка на статью или произвольный URL', 'knife-theme') ?></strong>
                <input class="option__input option__input--link" type="text" value="">
            </label>
        </div>

        <div class="option">
            <label>
                <strong><?php _e('Текст ссылки', 'knife-theme') ?></strong>
                <input class="option__input option__input--title" type="text" value="" placeholder="<?php _e('По умолчанию заголовок статьи', 'knife-theme'); ?>">
            </label>
        </div>

        <div class="option option--buttons">
            <?php
                printf('<button class="option__button option__button--append button" type="button">%s</button>',
                    __('Добавить', 'knife-theme')
                );

                printf('<button class="option__button option__button--poster button" type="button">%s</button>',
                    __('Выбрать свою обложку', 'knife-theme')
                );
            ?>

            <span class="spinner"></span>
        </div>
    </div>

    <div class="box box--items">
        <?php foreach($items as $i => $item) : ?>
            <div class="item <?php echo ($i === 0) ? 'hidden' : ''; ?>">
                <?php
                    printf('<p class="item__text">%s</p>',
                        esc_html($item['text'] ?? '')
                    );

                    printf('<a class="item__link" href="%1$s" target="_blank">%1$s</a>',
                        esc_url($item['link'] ?? '')
                    );

                    printf('<input class="item__text" name="%s[][text]" value="%s" type="hidden">',
                        esc_attr(self::$meta_items),
                        esc_attr($item['text'] ?? '')
                    );

                    printf('<input class="item__link" name="%s[][link]" value="%s" type="hidden">',
                        esc_attr(self::$meta_items),
                        esc_attr($item['link'] ?? '')
                    );
                ?>

                <span class="dashicons dashicons-menu"></span>
                <span class="dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
