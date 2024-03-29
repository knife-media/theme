<?php
$photo = '';

if ( isset( $user->ID ) ) {
    $photo = get_user_meta( $user->ID, self::$user_photo, true );
}
?>

<h2><?php esc_html_e( 'Дополнительные настройки профиля', 'knife-theme' ); ?></h2>

<table class="form-table hide-if-no-js">
    <tr>
        <th>
            <label><?php esc_html_e( 'Фото пользователя', 'knife-theme' ); ?></label>
        </th>

        <td>
            <div id="knife-user-photo" style="max-width: 150px;">
                <?php
                printf(
                    '<input class="photo" type="hidden" name="%s" value="%s">',
                    esc_attr( self::$user_photo ),
                    esc_url( $photo )
                );
                ?>

                <p>
                    <button class="button select" type="button"><?php esc_html_e( 'Загрузить', 'knife-theme' ); ?></button>
                    <button class="button remove hidden" type="button"><?php esc_html_e( 'Удалить', 'knife-theme' ); ?></button>

                    <span class="spinner"></span>
                </p>
            </div>
        </td>
    </tr>
</table>
