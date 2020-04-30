<?php
    $meta = get_term_meta($term->term_id, self::$term_meta, true);
?>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <label><?php _e('Цвет шапки записи', 'knife-theme') ?></label>
    </th>

    <td>
        <div class="knife-special-color">
            <?php
                printf('<input type="text" name="%s[single]" value="%s">',
                    esc_attr(self::$term_meta),
                    sanitize_text_field($meta['single'] ?? '')
                );
            ?>
        </div>
    </td>
</tr>
