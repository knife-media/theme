<div id="knife-quiz-box">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        if(count($items) < 1) {
            array_push($items, ['caption' => '', 'description' => '']);
        }

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">

    </div>

    <div class="box box--items">
        <?php foreach($items as $i => $item) : ?>
            <div class="item <?php echo ($i === 0) ? 'hidden' : ''; ?>">
                <div class="item__question question">
                    <?php
                        printf('<p class="question__title">%s<span>1</span></p>',
                            __('Вопрос #', 'knife-theme')
                        );

                        printf('<textarea class="question__field wp-editor-area" name="%s[][question]">%s</textarea>',
                            esc_attr(self::$meta_items),
                            esc_attr($item['question'] ?? '')
                        );

                        printf('<p class="question__shortcut">%s</p>',
                            wp_strip_all_tags($item['question'] ?? '', true)
                        );
                    ?>
                </div>

                <div class="item__answer answer">
                    <div class="answer__choice">
                        <?php
                            printf('<p class="answer__choice-title">%s</p>',
                                __('Вариант ответа', 'knife-theme')
                            );

                            printf('<textarea class="answer__choice-field wp-editor-area" name="%s[][choice]">%s</textarea>',
                                esc_attr(self::$meta_items),
                                esc_attr($item['choice'] ?? '')
                            );
                        ?>
                    </div>

                    <div class="answer__message">
                        <?php
                            printf('<p class="answer__message-title">%s</p>',
                                __('Текст после ответа', 'knife-theme')
                            );

                            printf('<textarea class="answer__message-field wp-editor-area" name="%s[][message]">%s</textarea>',
                                esc_attr(self::$meta_items),
                                esc_attr($item['message'] ?? '')
                            );
                        ?>
                    </div>

                    <div class="answer__options">
                        <?php
                            printf('<p class="answer__options-title">%s</p>',
                                __('Настройки ответа', 'knife-theme')
                            );

                            printf('<input class="answer__options-points" name="%s[][points]" value="%s">',
                                esc_attr(self::$meta_items),
                                esc_attr($item['points'] ?? '')
                            );
                        ?>
                    </div>
                </div>

                <div class="item__manage">
                    <?php
                        printf('<button class="item__manage-add button" type="button">%s</button>',
                            __('Добавить ответ', 'knife-theme')
                        );

                        printf('<button class="item__manage-move button" type="button">%s</button>',
                            __('Поднять выше', 'knife-theme')
                        );

                        printf('<button class="item__manage-manage button" type="button" disabled>%s</button>',
                            __('Настройки', 'knife-theme')
                        );
                    ?>
                </div>

                <span class="item__remove dashicons dashicons-trash"></span>
                <span class="item__toggle dashicons dashicons-editor-expand"></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="actions__add button button-primary" type="button">%s</button>',
                __('Добавить вопрос', 'knife-theme')
            );
        ?>
    </div>

    <div class="box box--results">
    </div>
</div>
