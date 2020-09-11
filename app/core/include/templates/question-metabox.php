<div id="knife-question-box">
    <?php
        $post_id = get_the_ID();

        // Get comments question meta
        $question = get_post_meta($post_id, self::$meta_question, true);

        // List of default avatars
        $emojis = ['🤔', '🧐', '😕', '🔥'];
    ?>

    <div class="question-content">
        <strong><?php _e('Текст вопроса:', 'knife-theme'); ?></strong>

        <?php
            printf(
                '<textarea class="question-message" name="%1$s[message]" placeholder="%3$s">%2$s</textarea>',
                esc_attr(self::$meta_question), esc_attr($question['message'] ?? ''),
                __('Что вы об этом думаете?', 'knife-theme')
            );
        ?>
    </div>

    <strong><?php _e('Подпись автора:', 'knife-theme'); ?></strong>

    <div class="question-meta">
        <?php
            printf(
                '<input class="question-author" type="text" name="%s[author]" value="%s" placeholder="%s">',
                esc_attr(self::$meta_question), esc_attr($question['author'] ?? ''),
                __('Редакция журнала «Нож» интересуется', 'knife-theme')
            );

            printf(
                '<input class="question-avatar" type="text" name="%s[avatar]" value="%s">',
                esc_attr(self::$meta_question), esc_attr($question['avatar'] ?? '')
            );
        ?>

        <div class="question-emojis">
            <?php
                foreach($emojis as $emoji) {
                    printf('<span>%s</span>', $emoji);
                }
            ?>
        </div>
    </div>
</div>