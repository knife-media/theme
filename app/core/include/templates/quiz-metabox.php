<div id="knife-quiz-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = (array) get_post_meta($post_id, self::$meta_options, true);

        if(!isset($options['details'])) {
            $options['details'] = 'none';
        }

        if(!isset($options['format'])) {
            $options['format'] = 'binary';
        }

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        // Add empty item
        array_unshift($items, []);

        // Quiz results
        $results = get_post_meta($post_id, self::$meta_results);

        // Add empty quiz result
        array_unshift($results, []);

        wp_nonce_field('metabox', self::$metabox_nonce);
    ?>

    <div class="box box--manage">
        <div class="manage">
            <?php
                printf(
                    '<p class="manage__title">%s</p>',
                    __('Настройки типа теста', 'knife-theme')
                );

                printf(
                    '<p class="manage__radio"><label><input data-format="binary" type="radio" name="%1$s[format]" value="binary"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['format'], 'binary', false),
                    __('Правильный / неправильный ответ', 'knife-theme')
                );

                printf(
                    '<p class="manage__radio"><label><input data-format="points" type="radio" name="%1$s[format]" value="points"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['format'], 'points', false),
                    __('Разные баллы за каждый ответ', 'knife-theme')
                );

                printf(
                    '<p class="manage__radio"><label><input data-format="category" type="radio" name="%1$s[format]" value="category"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['format'], 'category', false),
                    __('Наиболее часто выбираемый ответ', 'knife-theme')
                );

                printf(
                    '<p class="manage__title">%s</p>',
                    __('Отображениe вопросов', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input type="checkbox" name="%1$s[random]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['random']), true, false),
                    __('Показывать вопросы в случайном порядке', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input data-manage="poster" type="checkbox" name="%1$s[attachment]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['attachment']), true, false),
                    __('Использовать изображения в качестве ответов', 'knife-theme')
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
            ?>
        </div>

        <div class="manage">
            <?php
                printf(
                    '<p class="manage__title">%s</p>',
                    __('Внешний вид теста', 'knife-theme')
                );

                printf(
                    '<p class="manage__check"><label><input type="checkbox" name="%1$s[center]" value="1"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked(isset($options['center']), true, false),
                    __('Центрировать стартовый экран', 'knife-theme')
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_start]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_start'] ?? __('Начать тест', 'knife-theme')),
                    __('Текст на кнопке старта', 'knife-theme')
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_next]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_next'] ?? __('Продолжить', 'knife-theme')),
                    __('Текст на кнопке следующего вопроса', 'knife-theme')
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_last]" value="%2$s"></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['button_last'] ?? __('Посмотреть результаты', 'knife-theme')),
                    __('Текст на кнопке последнего вопроса', 'knife-theme')
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

                    $answers = $item['answers'] ?? [];

                    // Upgrade with default values
                    array_unshift($answers, array_fill_keys([], ''));
                ?>

                <div class="item__answers">

                    <?php foreach($answers as $j => $answer) : ?>
                        <div class="answer">
                            <figure class="answer__poster">
                                <?php
                                    if(!empty($answer['attachment'])) {
                                        printf('<img src="%s" alt="">',
                                            wp_get_attachment_image_url($answer['attachment'], 'short')
                                        );
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

                                    printf(
                                        '<div class="answer__category"><input type="text" maxlength="1" data-answer="category" value="%1$s"><strong>%2$s</strong></div>',
                                        esc_attr($answer['category'] ?? ''),
                                        __('Категория ответа', 'knife-theme')
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
                    __('Скрыть кнопку повторного прохождения', 'knife-theme')
                );
            ?>
        </div>

        <div class="summary">
            <?php
                printf(
                    '<p class="summary__title">%s</p>',
                    __('Параметры отображения текста', 'knife-theme')
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="none" type="radio" name="%1$s[details]" value="none"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['details'], 'none', false),
                    __('Не показывать текст под постером', 'knife-theme')
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="remark" type="radio" name="%1$s[details]" value="remark"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['details'], 'remark', false),
                    __('Общий текст для всех результатов', 'knife-theme')
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="result" type="radio" name="%1$s[details]" value="result"%2$s>%3$s</label></p>',
                    esc_attr(self::$meta_options),
                    checked($options['details'], 'result', false),
                    __('Отдельное описание для каждого результата', 'knife-theme')
                );
            ?>
        </div>
    </div>

    <div class="box box--remark">
        <div class="remark">
            <?php
                printf(
                    '<p class="remark__text"><textarea class="wp-editor-area" name="%1$s[remark]">%2$s</textarea></p>',
                    esc_attr(self::$meta_options),
                    esc_attr($options['remark'] ?? '')
                );
            ?>
        </div>
    </div>

    <div class="box box--results">

        <?php foreach($results as $i => $result) : ?>
            <div class="result">
                <?php
                    if(empty($result['posters']) || !is_array($result['posters'])) {
                        $result['posters'] = [''];
                    }
                ?>

                <div class="result__details">
                    <?php
                        printf(
                            '<textarea class="wp-editor-area" data-result="details">%s</textarea>',
                            esc_attr($result['details'] ?? '')
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
                            foreach($result['posters'] as $poster) {
                                if(!empty($poster)) {
                                    printf('<img src="%s" alt="">', esc_url($poster));
                                }

                                break;
                            }

                            printf(
                                '<figcaption class="result__image-caption">%s</figcaption>',
                                __('Выбрать изображение для постера', 'knife-theme')
                            );

                            printf(
                                '<input class="result__image-attachment" type="hidden" data-result="attachment" value="%s">',
                                esc_attr($result['attachment'] ?? '')
                            );
                        ?>
                    </figure>

                    <div class="result__image-footer">
                        <?php
                            if(method_exists('Knife_Poster_Templates', 'get_select')) {
                                Knife_Poster_Templates::get_select([
                                    'attributes' => [
                                        'class' => 'result__image-template',
                                        'data-result' => 'template'
                                    ],
                                    'selected' => esc_attr($result['template'] ?? '')
                                ]);

                                printf(
                                    '<button class="result__image-generate button" type="button">%s</button>',
                                    __('Сгенерировать', 'knife-theme')
                                );
                            }

                            printf(
                                '<button class="result__image-manual button" type="button">%s</button>',
                                __('Редактировать', 'knife-theme')
                            );
                        ?>

                        <span class="result__image-spinner spinner"></span>
                    </div>

                    <div class="result__image-warning"></div>
                </div>

                <div class="result__posters">

                    <?php foreach($result['posters'] as $score => $poster) : ?>
                        <p class="poster">
                            <?php
                                printf(
                                    '<strong class="poster__title">%1$s <span>%2$d</span></strong>',
                                    __('Постер для результата:', 'knife-theme'), (int) $score
                                );

                                printf(
                                    '<input class="poster__field" type="text" data-poster="%2$d" value="%1$s">',
                                    esc_url($poster ?? ''), (int) $score
                                );
                            ?>

                            <span class="poster__display dashicons dashicons-visibility"></span>
                        </p>
                    <?php endforeach; ?>

                </div>

                <div class="result__scores">
                    <?php
                        printf(
                            __('Показывать с %s по %s баллов или правильных ответов', 'knife-theme'),

                            sprintf('<input class="result__scores-field" type="number" data-result="from" value="%s">',
                                esc_attr($result['from'] ?? 0)
                            ),

                            sprintf('<input class="result__scores-field" type="number" data-result="to" value="%s">',
                                esc_attr($result['to'] ?? 0)
                            )
                        );
                    ?>
                </div>

                <div class="result__category">
                    <?php
                        printf(
                            __('Показывать для категории ответов %s', 'knife-theme'),

                            sprintf('<input class="result__category-field" type="text" data-result="category" value="%s">',
                                esc_attr($result['category'] ?? '')
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
