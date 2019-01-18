<div id="knife-select-box" class="hide-if-no-js">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, []);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">
        <div class="manage">
            <strong><?php _e('Ссылка на статью или произвольный URL', 'knife-theme') ?></strong>
            <input class="manage__input" type="text" value="">
        </div>

        <div class="manage">
            <?php
                printf('<button class="manage__append button" type="button">%s</button>',
                    __('Добавить', 'knife-theme')
                );
            ?>

            <span class="manage__spinner spinner"></span>
        </div>
    </div>

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item">
                <div class="option">
                    <strong><?php _e('Ссылка с карточки', 'knife-theme') ?></strong>

                    <?php
                        printf('<input class="option__link" name="%s[][link]" value="%s" type="text">',
                            esc_attr(self::$meta_items),
                            esc_attr($item['link'] ?? '')
                        );
                    ?>
                </div>

                <div class="option">
                    <strong><?php _e('Заголовок карточки', 'knife-theme') ?></strong>

                    <?php
                        printf('<textarea class="option__title" name="%s[][title]">%s</textarea>',
                            esc_attr(self::$meta_items),
                            esc_attr($item['title'] ?? '')
                        );
                    ?>
                </div>

                <figure class="option option--poster">
                    <?php
                        if(!empty($item['attachment'])) {
                            echo wp_get_attachment_image($item['attachment'], 'thumbnail', false, ['class' => 'option__image']);
                        }

                        printf('<figcaption class="option__blank">%s</figcaption>',
                            __('Выбрать изображение', 'knife-theme')
                        );

                        printf('<input class="option__attachment" type="hidden" name="%s[][attachment]" value="%s">',
                            esc_attr(self::$meta_items),
                            esc_attr($item['attachment'] ?? '')
                        );
                    ?>
                </figure>

                <span class="dashicons dashicons-menu"></span>
                <span class="dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>

    </div>
</div>
