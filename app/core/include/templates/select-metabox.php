<div id="knife-select-box" data-action="<?php echo esc_attr(self::$action); ?>" data-nonce="<?php echo wp_create_nonce(self::$nonce); ?>">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, [
            'text' => '',
            'link' => ''
        ]);
    ?>

    <div class="knife-select-manage">
        <p>
            <label>
                <strong><?php _e('Ссылка на статью или произвольный URL', 'knife-theme') ?></strong>
                <input class="input-link widefat" type="text" value="">
            </label>
        </p>

        <p>
            <label>
                <strong><?php _e('Текст ссылки', 'knife-theme') ?></strong>
                <input class="input-text widefat" type="text" value="" placeholder="<?php _e('По умолчанию заголовок статьи', 'knife-theme'); ?>">
            </label>
        </p>

        <fieldset>
            <a class="button button-append" href="#select-append"><?php _e('Добавить', 'knife-theme'); ?></a>
            <span class="spinner"></span>
        </fieldset>
    </div>

    <div class="knife-select-items">
        <?php foreach($items as $i => $item) : ?>
            <div class="knife-select-item <?php echo ($i === 0) ? 'hidden' : ''; ?>">
                <?php
                    printf('<p class="item-text">%s</p>',
                        esc_html($item['text']) ?? ''
                    );

                    printf('<a class="item-link" href="%1$s" target="_blank">%1$s</a>',
                        esc_url($item['link']) ?? ''
                    );

                    printf('<input class="item-text" name="%s[][text]" value="%s" type="hidden">',
                        esc_attr(self::$meta_items),
                        esc_attr($item['text']) ?? ''
                    );

                    printf('<input class="item-link" name="%s[][link]" value="%s" type="hidden">',
                        esc_attr(self::$meta_items),
                        esc_attr($item['link']) ?? ''
                    );
                ?>
                <span class="dashicons dashicons-menu"></span>
                <span class="dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
