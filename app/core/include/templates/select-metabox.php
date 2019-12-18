<div id="knife-select-box" class="hide-if-no-js">
    <?php
        $items = get_post_meta(get_the_ID(), self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, []);
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
                <?php
                    printf('<p class="item__link"><strong>%s</strong><input data-item="link" value="%s" type="text"></p>',
                        __('Ссылка с карточки', 'knife-theme'),
                        sanitize_text_field($item['link'] ?? '')
                    );

                    printf('<p class="item__title"><strong>%s</strong><textarea data-item="title" >%s</textarea></p>',
                        __('Заголовок карточки', 'knife-theme'),
                        wp_kses($item['title'] ?? '', self::$title_html)
                    );
                ?>

                <figure class="item__poster">
                    <?php
                        if(!empty($item['attachment'])) {
                            echo wp_get_attachment_image($item['attachment'], 'thumbnail', false, ['class' => 'item__poster-image']);
                        }
                    ?>

                    <figcaption class="item__poster-caption">+</figcaption>

                    <?php
                        printf('<input class="item__poster-attachment" type="hidden" data-item="attachment" value="%s">',
                            sanitize_text_field($item['attachment'] ?? '')
                        );
                    ?>
                </figure>

                <span class="item__change dashicons dashicons-arrow-up-alt"></span>
                <span class="item__delete dashicons dashicons-trash"></span>
            </div>
        <?php endforeach; ?>

    </div>
</div>
