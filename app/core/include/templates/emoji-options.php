<?php
    $meta = get_term_meta($term->term_id, self::$term_meta, true);
?>

<tr class="form-table">
    <th scope="row" valign="top">
        <label><?php _e('Эмодзи', 'knife-theme') ?></label>
    </th>

    <td>
        <?php
            printf('<input type="text" name="%1$s" value="%2$s">',
                esc_attr(self::$term_meta), esc_attr($meta)
            );
        ?>
    </td>
</tr>

