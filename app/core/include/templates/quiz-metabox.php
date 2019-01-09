<div id="knife-quiz-box">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, ['question' => '']);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">

    </div>

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item">
                <?php
                    printf('<p class="item__title">%s<span>%d</span></p>',
                        __('Вопрос #', 'knife-theme'),
                        absint($i)
                    );

                    printf('<p class="item__question"><textarea class="wp-editor-area" name="%2$s[%1$d][question]">%3$s</textarea></p>',
                        absint($i),
                        esc_attr(self::$meta_items),
                        esc_attr($item['question'] ?? '')
                    );

                    $answers = $item['answers'] ?? [];

                    // Upgrade with default values
                    array_unshift($answers, array_fill_keys(['choice', 'image', 'message'], ''));
                ?>

                <?php foreach($answers as $j => $answer) : ?>
                    <div class="item__answer answer">
                        <?php
                            printf(
                                '<div class="answer__choice"><label>%2$s</label><textarea class="wp-editor-area" name="%3$s">%1$s</textarea></div>',
                                esc_attr($answer['choice'] ?? ''),
                                __('Вариант ответа', 'knife-theme'),

                                sprintf('%s[%d][answers][][choice]',
                                    self::$meta_items,
                                    absint($i)
                                )
                            );

                            printf(
                                '<div class="answer__message"><label>%2$s</label><textarea class="wp-editor-area" name="%3$s">%1$s</textarea></div>',
                                esc_attr($answer['message'] ?? ''),
                                __('Текст после ответа', 'knife-theme'),

                                sprintf('%s[%d][answers][][message]',
                                    self::$meta_items,
                                    absint($i)
                                )
                            );
                        ?>
                    </div>
                <?php endforeach; ?>

                <div class="item__manage">
                    <?php
                        printf('<button class="item__manage-add button" type="button">%s</button>',
                            __('Добавить ответ', 'knife-theme')
                        );

                        printf('<button class="item__manage-toggle button" type="button" data-toggle="%s">%s</button>',
                            __('Свернуть ответы', 'knife-theme'),
                            __('Развернуть ответы', 'knife-theme')
                        );

                        printf('<button class="item__manage-manage button" type="button" disabled>%s</button>',
                            __('Настройки', 'knife-theme')
                        );
                    ?>
                </div>

                <span class="dashicons dashicons-trash" title="<?php _e('Удалить вопрос', 'knife-theme'); ?>"></span>
                <span class="dashicons dashicons-arrow-up-alt" title="<?php _e('Поднять выше', 'knife-theme'); ?>"></span>
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
