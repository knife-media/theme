<?php
    // Get term snippet meta
    $snippet = get_term_meta($term->term_id, self::$term_meta, true);
?>

<tr class="form-field">
    <th scope="row" valign="top">
        <label><?php _e('Адрес сниппета', 'knife-theme') ?></label>
    </th>

    <td>
        <?php
            printf('<input type="text" name="%s" value="%s">',
                esc_attr(self::$term_meta), esc_url($snippet)
            );
        ?>
    </td>
</tr>
