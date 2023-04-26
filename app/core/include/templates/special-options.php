<?php
    $meta = get_term_meta( $term->term_id, self::$term_meta, true );
?>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <?php esc_html_e( 'Скрытый спецпроект', 'knife-theme' ); ?>
    </th>

    <td>
        <?php
        printf(
            '<label><input type="checkbox" name="%1$s[hidden]" value="1"%3$s> %2$s</label>',
            esc_attr( self::$term_meta ),
            esc_html_e( 'Не отображать лейблы и вывеску', 'knife-theme' ),
            checked( $meta['hidden'] ?? 0, 1, false )
        );
        ?>
    </td>
</tr>

<tr class="form-field hide-if-no-js">
    <th scope="row" valign="top">
        <?php esc_html_e( 'Цвет шапки записи', 'knife-theme' ); ?>
    </th>

    <td>
        <div class="knife-special-color">
            <?php
                printf(
                    '<input type="text" name="%s[single]" value="%s">',
                    esc_attr( self::$term_meta ),
                    esc_attr( $meta['single'] ?? '' )
                );
                ?>
        </div>
    </td>
</tr>
