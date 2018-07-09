<?php
    $meta = get_term_meta($term->term_id, $this->meta, true);

    $sizes = [
        'auto' => __('Замостить фон', 'knife-theme'),
        'cover' => __('Растянуть изображение', 'knife-theme'),
        'contain' => __('Подогнать по ширине', 'knife-theme')
    ];
?>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Цвет фона', 'knife-theme') ?></label>
    </th>

    <td>
        <div class="knife-background-color">
            <?php
                printf('<input type="text" name="%s[color]" value="%s">',
                    esc_attr($this->meta), $meta['color'] ?? ''
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
        <div class="knife-background-image">
            <?php
                printf('<input type="hidden" name="%s[image]" value="%s">',
                    esc_attr($this->meta), $meta['image'] ?? ''
                );
            ?>

            <p>
                <button class="button select"><?php _e('Выбрать изображение', 'knife-theme'); ?></button>
                <button class="button remove"><?php _e('Удалить', 'knife-theme'); ?></a>
            </p>
        </div>
    </td>
</tr>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Размер изображения', 'knife-theme') ?></label>
    </th>

    <td>
        <select name="<?php echo esc_attr($this->meta); ?>[size]">
        <?php
            foreach($sizes as $name => $title) {
                printf('<option value="%1$s"%3$s>%2$s</option>', $name, $title, selected($meta['size'], $name, false));
            }
        ?>
        </select>
    </td>
</tr>
