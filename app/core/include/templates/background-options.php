<?php
    $sizes = [
        'auto' => __('Замостить фон', 'knife-theme'),
        'cover' => __('Растянуть изображение', 'knife-theme'),
        'contain' => __('Подогнать по размеру', 'knife-theme')
    ];

    // Get post background meta
    $background = (array) get_term_meta($term->term_id, self::$term_meta, true);

    // Set default background options
    $background = wp_parse_args($background, [
        'image' => '', 'size' => '', 'color' => ''
    ]);
?>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Цвет фона', 'knife-theme') ?></label>
    </th>

    <td>
        <div id="knife-background-color">
            <?php
                printf('<input class="color" type="text" name="%s[color]" value="%s">',
                    esc_attr(self::$term_meta), sanitize_hex_color($background['color'])
                );
            ?>
        </div>
    </td>
</tr>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Фоновое изображение', 'knife-theme') ?></label>
    </th>

    <td>
        <div id="knife-background-image" style="max-width: 300px;">
            <?php
                printf('<input class="image" type="hidden" name="%s[image]" value="%s">',
                    esc_attr(self::$term_meta), esc_url($background['image'])
                );
            ?>

            <p>
                <button class="button select" type="button"><?php _e('Выбрать изображение', 'knife-theme'); ?></button>
                <button class="button remove right" type="button" disabled><?php _e('Удалить', 'knife-theme'); ?></button>
            </p>

            <p style="margin-top: 10px;">
                <select class="size" name="<?php echo esc_attr(self::$term_meta); ?>[size]" style="width: 100%; max-width: 100%;" disabled>
                <?php
                    foreach($sizes as $name => $title) {
                        printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($background['size'], $name, false));
                    }
                ?>
                </select>
            </p>
        </div>
    </td>
</tr>
