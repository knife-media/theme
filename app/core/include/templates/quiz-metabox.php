<div id="knife-quiz-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        // Upgrade with default vaules
        array_unshift($items, []);

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--manage">

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
                            <div class="answer__data">
                                <?php
                                    printf(
                                        '<div class="answer__text answer__text--choice"><span>%2$s</span><textarea data-answer="choice">%1$s</textarea></div>',
                                        esc_attr($answer['choice'] ?? ''),
                                        __('Вариант ответа', 'knife-theme')
                                    );

                                    printf(
                                        '<div class="answer__text answer__text--message"><span>%2$s</span><textarea data-answer="message">%1$s</textarea></div>',
                                        esc_attr($answer['message'] ?? ''),
                                        __('Сообщение после ответа', 'knife-theme')
                                    );
                                ?>
                            </div>

                            <div class="answer__options">
                                <?php
                                    printf(
                                        '<div class="answer__points"><input type="number" data-answer="points" value="%1$s"><span>%2$s</span></div>',
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
                            '<button class="item__manage-toggle button" type="button" data-toggle="%s">%s</button>',
                            __('Свернуть ответы', 'knife-theme'),
                            __('Развернуть ответы', 'knife-theme')
                        );

                        printf(
                            '<button class="item__manage-options button" type="button" disabled>%s</button>',
                            __('Настройки', 'knife-theme')
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
            printf('<button class="actions__add button button-primary" type="button">%s</button>',
                __('Добавить вопрос', 'knife-theme')
            );
        ?>
    </div>

    <div class="box box--results">
    </div>
</div>
