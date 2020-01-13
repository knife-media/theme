<div id="knife-background-box" style="max-width: 300px;">
    <?php
        $post_id = get_the_ID();

        // Get post background meta
        $background = (array) get_post_meta($post_id, self::$meta_background, true);

        // Set default background options
        $background = wp_parse_args($background, [
            'image' => '', 'size' => ''
        ]);

        // Defien sizes array
        $sizes = [
            'auto' => __('Замостить фон', 'knife-theme'),
            'cover' => __('Растянуть изображение', 'knife-theme'),
            'contain' => __('Подогнать по размеру', 'knife-theme')
        ];
    ?>

    <p>
        <button class="button select" type="button"><?php _e('Выбрать изображение', 'knife-theme'); ?></button>
        <button class="button remove right" type="button" disabled><?php _e('Удалить', 'knife-theme'); ?></button>
    </p>

    <p>
        <select class="size" name="<?php echo esc_attr(self::$meta_background); ?>[size]" style="width: 100%;" disabled>
        <?php
            foreach($sizes as $name => $title) {
                printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($background['size'], $name, false));
            }
        ?>
        </select>
    </p>

    <p style="margin-bottom: 0;">
        <?php
            printf(
                '<label style="display:inline-block; margin-right:10px; padding-bottom: 5px;">%s</label>',
                __('Цвет фона: ', 'knife-theme')
            );

            printf(
                '<input class="color-picker" name="%s[color]" type="text" value="%s">',
                esc_attr(self::$meta_background),
                sanitize_text_field($background['color'] ?? ''),
            );
        ?>
    </p>

    <?php
        printf(
            '<input class="image" type="hidden" name="%s[image]" value="%s">',
            esc_attr(self::$meta_background), esc_url($background['image'])
        );
    ?>
</div>
