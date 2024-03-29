<div id="knife-quiz-box" class="hide-if-no-js">
    <?php
    // Quiz options
    $options = (array) get_post_meta( get_the_ID(), self::$meta_options, true );

    if ( ! isset( $options['details'] ) ) {
        $options['details'] = 'none';
    }

    if ( ! isset( $options['format'] ) ) {
        $options['format'] = 'binary';
    }

    // Quiz items
    $items = get_post_meta( get_the_ID(), self::$meta_items );

    // Define available categories array
    $categories = array();

    // Define ava

    // Add empty item
    array_unshift( $items, array() );

    // Quiz results
    $results = get_post_meta( get_the_ID(), self::$meta_results );

    // Add empty quiz result
    array_unshift( $results, array() );
    ?>

    <div class="box box--manage">
        <div class="manage">
            <?php
            printf(
                '<p class="manage__title">%s</p>',
                esc_html__( 'Настройки типа теста', 'knife-theme' )
            );

            printf(
                '<p class="manage__radio"><label><input data-format="binary" type="radio" name="%1$s[format]" value="binary"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( $options['format'], 'binary', false ),
                esc_html__( 'Правильный / неправильный ответ', 'knife-theme' )
            );

            printf(
                '<p class="manage__radio"><label><input data-format="points" type="radio" name="%1$s[format]" value="points"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( $options['format'], 'points', false ),
                esc_html__( 'Разные баллы за каждый ответ', 'knife-theme' )
            );

            printf(
                '<p class="manage__radio"><label><input data-format="category" type="radio" name="%1$s[format]" value="category"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( $options['format'], 'category', false ),
                esc_html__( 'Наиболее часто выбираемый ответ', 'knife-theme' )
            );

            printf(
                '<p class="manage__radio"><label><input data-format="dynamic" type="radio" name="%1$s[format]" value="dynamic"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( $options['format'], 'dynamic', false ),
                esc_html__( 'Динамический результат ответа', 'knife-theme' )
            );

            printf(
                '<p class="manage__title">%s</p>',
                esc_html__( 'Отображениe вопросов', 'knife-theme' )
            );

            printf(
                '<p class="manage__check"><label><input type="checkbox" name="%1$s[random]" value="1"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( isset( $options['random'] ), true, false ),
                esc_html__( 'Показывать вопросы в случайном порядке', 'knife-theme' )
            );

            printf(
                '<p class="manage__check"><label><input data-manage="poster" type="checkbox" name="%1$s[attachment]" value="1"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( isset( $options['attachment'] ), true, false ),
                esc_html__( 'Использовать изображения в качестве ответов', 'knife-theme' )
            );

            printf(
                '<p class="manage__check"><label><input data-manage="message" type="checkbox" name="%1$s[message]" value="1"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( isset( $options['message'] ), true, false ),
                esc_html__( 'Показывать сообщение после ответа на вопрос', 'knife-theme' )
            );

            printf(
                '<p class="manage__check"><label><input type="checkbox" name="%1$s[shuffle]" value="1"%2$s>%3$s</label></p>',
                esc_attr( self::$meta_options ),
                checked( isset( $options['shuffle'] ), true, false ),
                esc_html__( 'Перемешать ответы на вопросы', 'knife-theme' )
            );
            ?>
        </div>

        <div class="manage">
            <?php
                printf(
                    '<p class="manage__title">%s</p>',
                    esc_html__( 'Внешний вид теста', 'knife-theme' )
                );

                printf(
                    '<p class="manage__check"><label><input type="checkbox" name="%1$s[center]" value="1"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( isset( $options['center'] ), true, false ),
                    esc_html__( 'Центрировать стартовый экран', 'knife-theme' )
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_start]" value="%2$s"></p>',
                    esc_attr( self::$meta_options ),
                    esc_attr( $options['button_start'] ?? esc_html__( 'Начать тест', 'knife-theme' ) ),
                    esc_html__( 'Текст на кнопке старта', 'knife-theme' )
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_next]" value="%2$s"></p>',
                    esc_attr( self::$meta_options ),
                    esc_attr( $options['button_next'] ?? esc_html__( 'Продолжить', 'knife-theme' ) ),
                    esc_html__( 'Текст на кнопке следующего вопроса', 'knife-theme' )
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_last]" value="%2$s"></p>',
                    esc_attr( self::$meta_options ),
                    esc_attr( $options['button_last'] ?? esc_html__( 'Посмотреть результаты', 'knife-theme' ) ),
                    esc_html__( 'Текст на кнопке последнего вопроса', 'knife-theme' )
                );

                printf(
                    '<p class="manage__input"><strong>%3$s</strong><input type="text" name="%1$s[button_repeat]" value="%2$s"></p>',
                    esc_attr( self::$meta_options ),
                    esc_attr( $options['button_repeat'] ?? esc_html__( 'Пройти тест еще раз', 'knife-theme' ) ),
                    esc_html__( 'Текст на кнопке повтора', 'knife-theme' )
                );
                ?>
        </div>
    </div>

    <div class="box box--items">

        <?php foreach ( $items as $i => $item ) : ?>
            <div class="item">
                <?php
                printf(
                    '<p class="item__title">%s<span></span></p>',
                    esc_html__( 'Вопрос #', 'knife-theme' )
                );

                printf(
                    '<p class="item__question"><textarea class="wp-editor-area" data-item="question">%s</textarea></p>',
                    wp_kses_post( $item['question'] ?? '' )
                );

                $answers = $item['answers'] ?? array();

                // Upgrade with default values
                array_unshift( $answers, array_fill_keys( array(), '' ) );
                ?>

                <div class="item__answers">

                    <?php foreach ( $answers as $j => $answer ) : ?>
                        <div class="answer">
                            <figure class="answer__poster">
                                <?php
                                if ( ! empty( $answer['attachment'] ) ) {
                                    printf(
                                        '<img src="%s" alt="">',
                                        esc_attr( wp_get_attachment_image_url( $answer['attachment'], 'short' ) )
                                    );
                                }

                                printf(
                                    '<figcaption class="answer__poster-caption">%s</figcaption>',
                                    esc_html__( 'Выбрать изображение', 'knife-theme' )
                                );

                                printf(
                                    '<input class="answer__poster-attachment" type="hidden" data-answer="attachment" value="%s">',
                                    esc_attr( $answer['attachment'] ?? '' )
                                );
                                ?>
                            </figure>

                            <?php
                            printf(
                                '<div class="answer__text answer__text--choice"><strong>%2$s</strong><textarea data-answer="choice">%1$s</textarea></div>',
                                wp_kses_post( $answer['choice'] ?? '' ),
                                esc_html__( 'Вариант ответа', 'knife-theme' )
                            );

                            printf(
                                '<div class="answer__text answer__text--message"><strong>%2$s</strong><textarea data-answer="message">%1$s</textarea></div>',
                                wp_kses_post( $answer['message'] ?? '' ),
                                esc_html__( 'Сообщение после ответа', 'knife-theme' )
                            );
                            ?>

                            <div class="answer__options">
                                <?php
                                printf(
                                    '<div class="answer__binary"><label><input type="checkbox" data-answer="binary" value="1"%1$s>%2$s</label></div>',
                                    checked( isset( $answer['binary'] ), true, false ),
                                    esc_html__( 'Правильный ответ', 'knife-theme' )
                                );

                                printf(
                                    '<div class="answer__points"><input type="number" data-answer="points" value="%1$s"><strong>%2$s</strong></div>',
                                    absint( $answer['points'] ?? 0 ),
                                    esc_html__( 'Баллы за ответ', 'knife-theme' )
                                );

                                printf(
                                    '<div class="answer__category"><input type="text" maxlength="1" data-answer="category" value="%1$s"><strong>%2$s</strong></div>',
                                    esc_attr( $answer['category'] ?? '' ),
                                    esc_html__( 'Категория ответа', 'knife-theme' )
                                );

                                    // Append available category
                                if ( ! empty( $answer['category'] ) ) {
                                    $categories[] = $answer['category'];
                                }
                                ?>
                            </div>

                            <span class="answer__delete dashicons dashicons-no-alt" title="<?php esc_html_e( 'Удалить ответ', 'knife-theme' ); ?>"></span>
                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="item__manage">
                    <?php
                    printf(
                        '<button class="item__manage-add button" type="button">%s</button>',
                        esc_html__( 'Добавить ответ', 'knife-theme' )
                    );

                    printf(
                        '<button class="item__manage-toggle button" type="button" disabled data-toggle="%s">%s</button>',
                        esc_attr__( 'Свернуть ответы', 'knife-theme' ),
                        esc_html__( 'Развернуть ответы', 'knife-theme' )
                    );
                    ?>
                </div>

                <span class="item__delete dashicons dashicons-trash" title="<?php esc_attr__( 'Удалить вопрос', 'knife-theme' ); ?>"></span>
                <span class="item__change dashicons dashicons-arrow-up-alt" title="<?php esc_attr__( 'Поднять выше', 'knife-theme' ); ?>"></span>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf(
                '<button class="action action--item button button-primary" type="button">%s</button>',
                esc_html__( 'Добавить вопрос', 'knife-theme' )
            );
            ?>
    </div>

    <div class="box box--summary">
        <div class="summary">
            <?php
                printf(
                    '<p class="summary__title">%s</p>',
                    esc_html__( 'Настройки результатов', 'knife-theme' )
                );

                printf(
                    '<p class="summary__check"><label><input data-summary="achievment" type="checkbox" name="%1$s[achievment]" value="1"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( isset( $options['achievment'] ), true, false ),
                    esc_html__( 'Показывать количество набранных ответов', 'knife-theme' )
                );

                printf(
                    '<p class="summary__check"><label><input type="checkbox" name="%1$s[noshare]" value="1"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( isset( $options['noshare'] ), true, false ),
                    esc_html__( 'Шарить страницу теста, вместо результата', 'knife-theme' )
                );

                printf(
                    '<p class="summary__check"><label><input type="checkbox" name="%1$s[norepeat]" value="1"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( isset( $options['norepeat'] ), true, false ),
                    esc_html__( 'Скрыть кнопку повторного прохождения', 'knife-theme' )
                );

                ?>
        </div>

        <div class="summary">
            <?php
                printf(
                    '<p class="summary__title">%s</p>',
                    esc_html__( 'Параметры отображения текста', 'knife-theme' )
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="none" type="radio" name="%1$s[details]" value="none"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( $options['details'], 'none', false ),
                    esc_html__( 'Не показывать текст под постером', 'knife-theme' )
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="remark" type="radio" name="%1$s[details]" value="remark"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( $options['details'], 'remark', false ),
                    esc_html__( 'Общий текст для всех результатов', 'knife-theme' )
                );

                printf(
                    '<p class="summary__radio"><label><input data-details="result" type="radio" name="%1$s[details]" value="result"%2$s>%3$s</label></p>',
                    esc_attr( self::$meta_options ),
                    checked( $options['details'], 'result', false ),
                    esc_html__( 'Отдельное описание для каждого результата', 'knife-theme' )
                );
                ?>
        </div>
    </div>

    <div class="box box--remark">
        <div class="remark">
            <?php
                printf(
                    '<p class="remark__text"><textarea class="wp-editor-area" name="%1$s[remark]">%2$s</textarea></p>',
                    esc_attr( self::$meta_options ),
                    wp_kses_post( $options['remark'] ?? '' )
                );
                ?>
        </div>
    </div>

    <div class="box box--results">

        <?php foreach ( $results as $i => $result ) : ?>
            <div class="result">
                <?php
                if ( empty( $result['posters'] ) || ! is_array( $result['posters'] ) ) {
                    $result['posters'] = array( '' );
                }

                if ( ! array_key_exists( 'category', $result ) ) {
                    $result['category'] = '';
                }

                if ( ! array_key_exists( 'dynamic', $result ) ) {
                    $result['dynamic'] = '';
                }
                ?>

                <div class="result__details">
                    <?php
                        printf(
                            '<textarea class="wp-editor-area" data-result="details">%s</textarea>',
                            wp_kses_post( $result['details'] ?? '' )
                        );
                    ?>
                </div>

                <div class="result__share">
                    <div class="result__share-block">
                        <?php
                            printf(
                                '<p class="result__share-achievment"><strong>%2$s</strong><input type="text" data-result="achievment" value="%1$s" placeholder="%3$s"></p>',
                                esc_attr( $result['achievment'] ?? '' ),
                                esc_html__( 'Результат', 'knife-theme' ),
                                esc_attr__( '% из 10', 'knife-theme' )
                            );

                            printf(
                                '<p class="result__share-heading"><strong>%2$s</strong><input type="text" data-result="heading" value="%1$s"></p>',
                                esc_attr( $result['heading'] ?? '' ),
                                esc_html__( 'Заголовок результата', 'knife-theme' )
                            );
                        ?>
                    </div>

                    <?php
                        printf(
                            '<p class="result__share-description"><strong>%2$s</strong><textarea data-result="description">%1$s</textarea></p>',
                            wp_kses_post( $result['description'] ?? '' ),
                            esc_html__( 'Описание', 'knife-theme' )
                        );
                    ?>

                    <div class="result__share-posters">

                        <?php foreach ( $result['posters'] as $score => $poster ) : ?>
                            <p class="poster">
                                <?php
                                    printf(
                                        '<strong class="poster__title">%1$s <span>%2$d</span></strong>',
                                        esc_html__( 'Постер для результата:', 'knife-theme' ),
                                        (int) $score
                                    );

                                    printf(
                                        '<input class="poster__field" type="text" data-poster="%2$d" value="%1$s">',
                                        esc_url( $poster ?? '' ),
                                        (int) $score
                                    );
                                ?>

                                <span class="poster__display dashicons dashicons-visibility"></span>
                            </p>
                        <?php endforeach; ?>

                    </div>

                </div>

                <div class="result__image">
                    <div class="result__image-block">
                        <figure class="result__image-poster">
                            <?php
                            foreach ( $result['posters'] as $poster ) {
                                if ( ! empty( $poster ) ) {
                                    printf( '<img src="%s" alt="">', esc_url( $poster ) );
                                }

                                break;
                            }
                            ?>

                            <figcaption class="result__image-caption">+</figcaption>

                            <?php

                                printf(
                                    '<input class="result__image-attachment" type="hidden" data-result="attachment" value="%s">',
                                    esc_attr( $result['attachment'] ?? '' )
                                );
                            ?>
                        </figure>

                        <div class="result__image-warning"></div>

                        <div class="result__image-footer">
                            <?php
                            if ( method_exists( 'Knife_Poster_Templates', 'print_select' ) ) {
                                Knife_Poster_Templates::print_select(
                                    array(
                                        'target'     => 'quiz',
                                        'attributes' => array(
                                            'class'       => 'result__image-template',
                                            'data-result' => 'template',
                                        ),
                                        'selected'   => esc_attr( $result['template'] ?? '' ),
                                    )
                                );

                                printf(
                                    '<button class="result__image-generate button" type="button">%s</button>',
                                    esc_html__( 'Сгенерировать', 'knife-theme' )
                                );
                            }

                                printf(
                                    '<button class="result__image-manual button" type="button">%s</button>',
                                    esc_html__( 'Редактировать', 'knife-theme' )
                                );
                            ?>

                            <span class="result__image-spinner spinner"></span>
                        </div>
                    </div>
                </div>

                <div class="result__scores">
                    <?php
                        printf(
                            __( 'Показывать с %1$s по %2$s баллов или правильных ответов', 'knife-theme' ), // phpcs:ignore
                            sprintf(
                                '<input class="result__scores-field" type="number" data-result="from" value="%s">',
                                absint( $result['from'] ?? 0 )
                            ),
                            sprintf(
                                '<input class="result__scores-field" type="number" data-result="to" value="%s">',
                                absint( $result['to'] ?? 0 )
                            )
                        );

                        printf(
                            '<p class="result__scores-warning dashicons dashicons-warning" title="%s"></p>',
                            esc_attr__( 'Необходимо обновить постеры', 'knife-theme' )
                        );
                    ?>
                </div>

                <div class="result__category">
                    <?php
                        printf(
                            '<strong class="result__category-title">%s</strong>',
                            esc_html__( 'Показывать для ответов', 'knife-theme' )
                        );
                    ?>

                    <select class="result__category-select" data-result="category">
                        <?php
                        foreach ( array_unique( $categories ) as $category ) {
                            printf(
                                '<option value="%1$s"%2$s>%1$s</option>',
                                esc_attr( $category ),
                                selected( $result['category'], $category, false )
                            );
                        }

                            printf(
                                '<option value="default"%2$s>%1$s</option>',
                                esc_html__( 'по умолчанию', 'knife-theme' ),
                                selected( $result['category'], 'default', false )
                            );
                        ?>
                    </select>

                    <?php
                        printf(
                            '<strong class="result__category-title">%s</strong>',
                            esc_html__( 'когда их выбрано не менее', 'knife-theme' )
                        );

                        printf(
                            '<input class="result__category-minimum" data-result="minimum" type="text" value="%s">',
                            absint( $result['minimum'] ?? 0 )
                        );
                    ?>
                </div>

                <div class="result__dynamic">
                    <?php
                        printf(
                            '<strong class="result__dynamic-title">%s</strong>',
                            esc_html__( 'Ссылка на генератор постера', 'knife-theme' )
                        );

                        printf(
                            '<input class="result__dynamic-field" data-result="dynamic" type="text" value="%s">',
                            esc_attr( $result['dynamic'] )
                        );
                    ?>
                </div>

                <span class="result__delete dashicons dashicons-trash" title="<?php esc_attr_e( 'Удалить результат', 'knife-theme' ); ?>"></span>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf(
                '<button class="action action--result button button-primary" type="button">%s</button>',
                esc_html__( 'Добавить результат', 'knife-theme' )
            );
            ?>
    </div>
</div>
