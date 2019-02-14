<div id="knife-ask-box">
    <?php
        $options = (array) get_post_meta(get_the_ID(), self::$meta_options, true);

        if(empty($options['counter'])) {
            $options['counter'] = (int) get_option(self::$option_counter, 0) + 1;
        }

        printf(
            '<p><strong>%s</strong>%s<span class="howto">%s</span></p>',
            __('Автор вопроса', 'knife-theme'),

            sprintf('<input type="text" class="widefat" name="%s[asker]" value="%s" style="margin:5px 0;">',
                esc_attr(self::$meta_options),
                esc_attr($options['asker'] ?? '')
            ),

            __('Описание можно отделить запятой', 'knife-theme')
        );

        printf(
            '<div><strong style="margin-right:10px;">%s</strong>%s</div>',
            __('Номер вопроса', 'knife-theme'),

            sprintf('<input type="number" class="small-text" name="%s[counter]" value="%s">',
                esc_attr(self::$meta_options),
                esc_attr($options['counter'])
            )
        );

        wp_nonce_field('metabox', self::$metabox_nonce);
    ?>
</div>

