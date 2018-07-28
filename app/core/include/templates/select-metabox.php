<div id="knife-selection-box">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta . '-items');

        // upgrade with default vaules
        array_unshift($items, ['text' => '', 'link' => '']);
    ?>

    <div class="knife-selection-manage">
        <p>
            <label for="knife-selection-link"><strong><?php _e('Ссылка', 'knife-theme') ?></strong></label>
            <input id="knife-selection-link" class="widefat" value="">
        </p>

        <p>
            <label for="knife-selection-text"><strong><?php _e('Заголовок', 'knife-theme') ?></strong></label>
            <input id="knife-selection-text" class="widefat" value="">
        </p>

        <fieldset>
            <a id="knife-selection-add" href="#selection-add" class="button"><?php _e('Добавить', 'knife-theme'); ?></a>
        </fieldset>
    </div>

    <div class="knife-selection-items">

    <?php foreach($items as $i => $item) : ?>
        <div class="knife-selection-item <?php echo ($i === 0) ? 'hidden' : ''; ?>">
            <div class="item-text">
                <?php
                    printf('<h1>%s</h1>',
                        esc_html($item['text'])
                    );

                    printf('<input name="%1$s" value="%2$s" type="hidden">',
                        self::$meta . '-items[][text]', $item['text'] ?? ''
                    );
                ?>
            </div>

            <div class="item-link">
                <?php
                    printf('<a href="%1$s" target="_blank">%1$s</a>',
                        esc_url($item['link'])
                    );

                    printf('<input name="%1$s" value="%2$s" type="hidden">',
                        self::$meta . '-items[][link]', $item['link'] ?? ''
                    );
                ?>
            </div>

            <span class="dashicons dashicons-menu"></span>
            <span class="dashicons dashicons-trash"></span>
        </div>
    <?php endforeach; ?>

    </div>
</div>
