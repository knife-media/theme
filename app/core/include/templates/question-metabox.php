<div id="knife-question-box">
    <?php
    // Get comments question meta
    $question = get_post_meta( get_the_ID(), self::$meta_question, true );

    // List of default avatars
    $emojis = array( '🤔', '🧐', '😕', '🔥' );
    ?>

    <div class="question-content">
        <strong><?php esc_html_e( 'Текст вопроса:', 'knife-theme' ); ?></strong>

        <?php
            printf(
                '<textarea class="question-message" name="%1$s[message]" placeholder="%3$s">%2$s</textarea>',
                esc_attr( self::$meta_question ),
                esc_attr( $question['message'] ?? '' ),
                esc_html__( 'Что вы об этом думаете?', 'knife-theme' )
            );
            ?>
    </div>

    <strong><?php esc_html_e( 'Подпись автора:', 'knife-theme' ); ?></strong>

    <div class="question-meta">
        <?php
        printf(
            '<input class="question-author" type="text" name="%s[author]" value="%s" placeholder="%s">',
            esc_attr( self::$meta_question ),
            esc_attr( $question['author'] ?? '' ),
            esc_attr__( 'Редакция журнала «Нож» интересуется', 'knife-theme' )
        );

        printf(
            '<input class="question-avatar" type="text" name="%s[avatar]" value="%s">',
            esc_attr( self::$meta_question ),
            esc_attr( $question['avatar'] ?? '' )
        );
        ?>

        <div class="question-emojis">
            <?php
            foreach ( $emojis as $emoji ) {
                printf( '<span>%s</span>', esc_html( $emoji ) );
            }
            ?>
        </div>
    </div>
</div>
