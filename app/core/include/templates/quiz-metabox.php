<div id="knife-quiz-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        // Add empty item
        array_unshift($items, []);

        // Quiz results
        $results = get_post_meta($post_id, self::$meta_results);

        // Add empty quiz result
        array_unshift($results, []);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">
        <div class="manage">
            <?php
                printf(
                    '<p class="manage__title">%s</p>',
                    __('Настройки отображения вопросов', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input data-manage="poster" type="checkbox" name="%1$s[image]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['image']), true, false),
                    __('Использовать изображения в качестве ответов', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input data-manage="points" type="checkbox" name="%1$s[points]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['points']), true, false),
                    __('Разные баллы за каждый ответ', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input data-manage="message" type="checkbox" name="%1$s[message]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['message']), true, false),
                    __('Показывать сообщение после ответа на вопрос', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input type="checkbox" name="%1$s[shuffle]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['shuffle']), true, false),
                    __('Перемешать ответы на вопросы', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input type="checkbox" name="%1$s[subtitle]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['subtitle']), true, false),
                    __('Отображать правильно/неправильно в ответе', 'knife-theme')
                );
            ?>
        </div>

        <div class="manage">
            <?php
                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_text]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_text'] ?? __('Начать тест', 'knife-theme')),
                    __('Текст на кнопке', 'knife-theme')
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_next]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_next'] ?? __('Следующий вопрос', 'knife-theme')),
                    __('Текст на кнопке следующего вопроса', 'knife-theme')
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_repeat]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_repeat'] ?? __('Пройти тест еще раз', 'knife-theme')),
                    __('Текст на кнопке повтора', 'knife-theme')
                );
            ?>
        </div>
    </div>

    <div class="box box--items">

        <?php foreach($items as $i => $item) : ?>
            <div class="item">
                <?php
                    printf(
                        '<p class="item__title">%s<span></span></p>',
                        __('Вопрос #', 'knife-theme')
                    );

                    printf(
                        '<p class="item__question"><textarea class="wp-editor-area" data-item="question">%s</textarea></p>',
                        esc_attr($item['question'] ?? '')
                    );

                    $answers = $item['answer'] ?? [];

                    // Upgrade with default values
                    array_unshift($answers, array_fill_keys([], ''));
                ?>

                <div class="item__answers">

                    <?php foreach($answers as $j => $answer) : ?>
                        <div class="answer">
                            <figure class="answer__poster">
                                <?php
                                    if(!empty($answer['attachment'])) {
                                        echo wp_get_attachment_image($answer['attachment'], 'thumbnail');
                                    }

                                    printf(
                                        '<figcaption class="answer__poster-caption">%s</figcaption>',
                                        __('Выбрать изображение', 'knife-theme')
                                    );

                                    printf(
                                        '<input class="answer__poster-attachment" type="hidden" data-answer="attachment" value="%s">',
                                        esc_attr($answer['attachment'] ?? '')
                                    );
                                ?>
                            </figure>

                            <?php
                                printf(
                                    '<div class="answer__text answer__text--choice"><strong>%2$s</strong><textarea data-answer="choice">%1$s</textarea></div>',
                                    esc_attr($answer['choice'] ?? ''),
                                    __('Вариант ответа', 'knife-theme')
                                );

                                printf(
                                    '<div class="answer__text answer__text--message"><strong>%2$s</strong><textarea data-answer="message">%1$s</textarea></div>',
                                    esc_attr($answer['message'] ?? ''),
                                    __('Сообщение после ответа', 'knife-theme')
                                );
                            ?>

                            <div class="answer__options">
                                <?php
                                    printf(
                                        '<div class="answer__binary"><label><input type="checkbox" data-answer="binary" value="1"%1$s>%2$s</label></div>',
                                        checked(isset($answer['binary']), true, false),
                                        __('Правильный ответ', 'knife-theme')
                                    );

                                    printf(
                                        '<div class="answer__points"><input type="number" data-answer="points" value="%1$s"><strong>%2$s</strong></div>',
                                        esc_attr($answer['points'] ?? 0),
                                        __('Баллы за ответ', 'knife-theme')
                                    );
                                ?>
                            </div>

                            <span class="answer__delete dashicons dashicons-no-alt" title="<?php _e('Удалить ответ', 'knife-theme'); ?>"></span>
                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="item__manage">
                    <?php
                        printf(
                            '<button class="item__manage-add button" type="button">%s</button>',
                            __('Добавить ответ', 'knife-theme')
                        );

                        printf(
                            '<button class="item__manage-toggle button" type="button" disabled data-toggle="%s">%s</button>',
                            __('Свернуть ответы', 'knife-theme'),
                            __('Развернуть ответы', 'knife-theme')
                        );
                    ?>
                </div>

                <span class="item__delete dashicons dashicons-trash" title="<?php _e('Удалить вопрос', 'knife-theme'); ?>"></span>
                <span class="item__change dashicons dashicons-arrow-up-alt" title="<?php _e('Поднять выше', 'knife-theme'); ?>"></span>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="action action--item button button-primary" type="button">%s</button>',
                __('Добавить вопрос', 'knife-theme')
            );
        ?>
    </div>

    <div class="box box--summary">
        <div class="summary">
            <?php
                printf(
                    '<p class="summary__title">%s</p>',
                    __('Настройки результатов', 'knife-theme')
                );

                printf(
                    '<p class="summary__check"><label><input data-summary="common" type="checkbox" name="%1$s[common]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['common']), true, false),
                    __('Общий текст для всех результатов', 'knife-theme')
                );

                printf(
                    '<p class="summary__check"><label><input data-summary="achievment" type="checkbox" name="%1$s[achievment]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['achievment']), true, false),
                    __('Показывать количество набранных ответов', 'knife-theme')
                );

                printf(
                    '<p class="summary__check"><label><input type="checkbox" name="%1$s[noshare]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['noshare']), true, false),
                    __('Шарить страницу теста, вместо результата', 'knife-theme')
                );

                printf(
                    '<p class="summary__check"><label><input type="checkbox" name="%1$s[norepeat]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['norepeat']), true, false),
                    __('Спрятать кнопку повтора прохождения', 'knife-theme')
                );

                printf(
                    '<p class="summary__text"><textarea class="wp-editor-area" name="%1$s[remark]">%2$s</textarea></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['remark'] ?? '')
                );
            ?>
        </div>
    </div>

    <div class="box box--results">

        <?php foreach($results as $i => $result) : ?>
            <div class="result">
                <div class="result__message">
                    <?php
                        printf(
                            '<textarea class="wp-editor-area" data-result="message">%s</textarea>',
                            esc_attr($result['message'] ?? '')
                        );
                    ?>
                </div>

                <div class="result__share">
                    <?php
                        printf(
                            '<p class="result__share-achievment"><strong>%2$s</strong><input type="text" data-result="achievment" value="%1$s" placeholder="%3$s"></p>',
                            esc_attr($result['achievment'] ?? ''),
                            __('Результат', 'knife-theme'),
                            __('% из 10', 'knife-theme')
                        );

                        printf(
                            '<p class="result__share-heading"><strong>%2$s</strong><input type="text" data-result="heading" value="%1$s"></p>',
                            esc_attr($result['heading'] ?? ''),
                            __('Заголовок результата', 'knife-theme')
                        );

                        printf(
                            '<p class="result__share-description"><strong>%2$s</strong><textarea data-result="description">%1$s</textarea></p>',
                            esc_attr($result['description'] ?? ''),
                            __('Описание', 'knife-theme')
                        );
                    ?>
                </div>

                <div class="result__image">
                    <figure class="result__image-poster">
                        <?php
                            if(!empty($result['media'])) {
                                printf('<img src="%s" alt="">', esc_url($result['media']));
                            }

                            printf(
                                '<figcaption class="result__image-caption">%s</figcaption>',
                                __('Выбрать изображение для постера', 'knife-theme')
                            );

                            printf(
                                '<input type="hidden" data-result="media" value="%s">',
                                esc_attr($result['media'] ?? '')
                            );

                            printf(
                                '<input type="hidden" data-result="attachment" value="%s">',
                                esc_attr($result['attachment'] ?? '')
                            );
                        ?>
                    </figure>

                    <div class="result__image-footer">
                        <?php
                            printf(
                                '<button class="result__image-generate button" type="button">%s</button>',
                                __('Сгенерировать', 'knife-theme')
                            );
                        ?>

                        <span class="result__image-spinner spinner"></span>
                    </div>
                </div>

                <p class="result__warning"></p>

                <div class="result__scores">
                    <?php
                        printf(
                            __('Показывать с %s по %s баллов или правильных ответов', 'knife-theme'),

                            sprintf('<input type="number" data-result="from" value="%s">',
                                esc_attr($result['from'] ?? 0)
                            ),

                            sprintf('<input type="number" data-result="to" value="%s">',
                                esc_attr($result['to'] ?? 0)
                            )
                        );
                    ?>
                </div>


                <span class="result__delete dashicons dashicons-trash" title="<?php _e('Удалить результат', 'knife-theme'); ?>"></span>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="action action--result button button-primary" type="button">%s</button>',
                __('Добавить результат', 'knife-theme')
            );
        ?>
    </div>
</div>
