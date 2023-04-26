<div id="knife-story-box">
    <?php
        // Story options
        $options = get_post_meta( get_the_ID(), self::$meta_options, true );

        // Story items
        $stories = get_post_meta( get_the_ID(), self::$meta_items );

        // Upgrade with default vaules
        array_unshift( $stories, array() );
    ?>


    <div class="box box--manage">
        <div class="manage manage--background">
            <figure class="manage__background">
                <?php
                if ( ! empty( $options['background'] ) ) {
                    printf(
                        '<img class="manage__background-image" src="%s" alt="">',
                        esc_url( $options['background'] )
                    );
                }

                    printf(
                        '<figcaption class="manage__background-blank">%s</figcaption>',
                        esc_html__( 'Выбрать изображение', 'knife-theme' )
                    );

                    printf(
                        '<input class="manage__background-media" type="hidden" name="%s[background]" value="%s">',
                        esc_attr( self::$meta_options ),
                        esc_url( $options['background'] ?? '' )
                    );
                    ?>
            </figure>
        </div>

        <div class="manage manage--settings">
            <div class="manage__item">
                <?php
                printf(
                    '<label>%s</label><input class="manage__item-shadow" type="range" name="%s[shadow]" min="0" max="100" step="5" value="%s">',
                    esc_html__( 'Затемнение фона', 'knife-theme' ),
                    esc_attr( self::$meta_options ),
                    absint( $options['shadow'] ?? 0 )
                );
                ?>
            </div>

            <div class="manage__item">
                <?php
                printf(
                    '<label>%s</label><input class="manage__item-blur" type="range" name="%s[blur]" min="0" max="10" step="1" value="%s">',
                    esc_html__( 'Размытие фона', 'knife-theme' ),
                    esc_attr( self::$meta_options ),
                    absint( $options['blur'] ?? 0 )
                );
                ?>
            </div>

            <div class="manage__item">
                <?php
                    printf(
                        '<label>%s</label><input class="manage__item-color" name="%s[color]" value="%s">',
                        esc_html__( 'Цвет фона', 'knife-theme' ),
                        esc_attr( self::$meta_options ),
                        sanitize_hex_color( $options['color'] ?? '' )
                    );
                    ?>
            </div>
        </div>
    </div>

    <div class="box box--items">

        <?php foreach ( $stories as $i => $story ) : ?>
            <div class="item">
                <?php
                if ( ! empty( $story['media'] ) ) {
                    printf(
                        '<img class="item__image" src="%s" alt="">',
                        esc_url( wp_get_attachment_thumb_url( $story['media'] ) )
                    );
                }

                    printf(
                        '<input class="item__media" data-item="media" type="hidden" value="%s">',
                        esc_attr( $story['media'] ?? '' )
                    );

                    printf(
                        '<textarea class="item__entry wp-editor-area" data-item="entry">%s</textarea>',
                        wp_kses_post( $story['entry'] ?? '' )
                    );
                ?>

                <div class="item__field">
                    <?php
                        printf(
                            '<span class="item__field-image" title="%s"></span>',
                            esc_attr__( 'Добавить медиафайл', 'knife-theme' )
                        );

                        printf(
                            '<span class="item__field-clear" title="%s"></span>',
                            esc_attr__( 'Удалить медиафайл', 'knife-theme' )
                        );

                        printf(
                            '<span class="item__field-trash" title="%s"></span>',
                            esc_attr__( 'Удалить слайд', 'knife-theme' )
                        );
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <?php
            printf(
                '<button class="actions__add button" type="button">%s</button>',
                esc_html__( 'Добавить элемент', 'knife-theme' )
            );
            ?>
    </div>
</div>
