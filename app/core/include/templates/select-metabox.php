<div id="knife-select-box" data-action="<?php echo esc_attr(self::$action); ?>" data-nonce="<?php echo wp_create_nonce(self::$nonce); ?>">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta . '-items');

        // Upgrade with default vaules
        array_unshift($items, ['text' => '', 'link' => '']);
    ?>

    <div class="knife-select-manage">
        <p>
            <label>
                <strong><?php _e('Ссылка на статью или произвольный URL', 'knife-theme') ?></strong>
                <input class="input-link widefat" value="">
            </label>
        </p>

        <p>
            <label>
                <strong><?php _e('Текст ссылки', 'knife-theme') ?></strong>
                <input class="input-text widefat" value="" placeholder="<?php _e('По умолчанию заголовок статьи', 'knife-theme'); ?>">
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
                <div class="item-text">
                    <?php
                        printf('<h1>%s</h1>',
                            esc_html($item['text'] ?? '')
                        );

                        printf('<input name="%1$s" value="%2$s" type="hidden">',
                            self::$meta . '-items[][text]', esc_attr($item['text'] ?? '')
                        );
                    ?>
                </div>

                <div class="item-link">
                    <?php
                        printf('<a href="%1$s" target="_blank">%1$s</a>',
                            esc_url($item['link'] ?? '')
                        );

                        printf('<input name="%1$s" value="%2$s" type="hidden">',
                            self::$meta . '-items[][link]', esc_attr($item['link'] ?? '')
                        );
                    ?>
                </div>

                <span class="dashicons dashicons-menu"></span>
                <span class="dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
