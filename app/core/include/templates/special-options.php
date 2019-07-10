<?php
    $meta = get_term_meta($term->term_id, self::$term_meta, true);
?>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Цвет шапки', 'knife-theme') ?></label>
    </th>

    <td>
        <div class="knife-special-color">
            <?php
                printf('<input type="text" name="%s[color]" value="%s">',
                    esc_attr(self::$term_meta),
                    sanitize_text_field($meta['color'] ?? '')
                );
            ?>
        </div>
    </td>
</tr>
