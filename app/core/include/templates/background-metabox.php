<div id="knife-background-box" style="max-width: 300px;">
    <?php
    // Get post background meta
    $background = (array) get_post_meta( get_the_ID(), self::$meta_background, true );

    // Set default background options
    $background = wp_parse_args(
        $background,
        array(
            'image' => '',
            'size'  => '',
        )
    );

    // Defien sizes array
    $sizes = array(
        'auto'    => esc_html__( 'Замостить фон', 'knife-theme' ),
        'cover'   => esc_html__( 'Растянуть изображение', 'knife-theme' ),
        'contain' => esc_html__( 'Подогнать по размеру', 'knife-theme' ),
    );
    ?>

    <p>
        <button class="button select" type="button"><?php esc_html_e( 'Выбрать изображение', 'knife-theme' ); ?></button>
        <button class="button remove right" type="button" disabled><?php esc_html_e( 'Удалить', 'knife-theme' ); ?></button>
    </p>

    <p>
        <select class="size" name="<?php echo esc_attr( self::$meta_background ); ?>[size]" style="width: 100%;" disabled>
        <?php
        foreach ( $sizes as $name => $caption ) {
            printf(
                '<option value="%1$s"%3$s>%2$s</option>',
                esc_attr( $name ),
                esc_html( $caption ),
                selected( $background['size'], $name, false )
            );
        }
        ?>
        </select>
    </p>

    <p style="margin-bottom: 0;">
        <?php
        printf(
            '<label style="display:inline-block; margin-right:10px; padding-bottom: 5px;">%s</label>',
            esc_html__( 'Цвет фона: ', 'knife-theme' )
        );

        printf(
            '<input class="color-picker" name="%s[color]" type="text" value="%s">',
            esc_attr( self::$meta_background ),
            esc_html( $background['color'] ?? '' ),
        );
        ?>
    </p>

    <?php
    printf(
        '<input class="image" type="hidden" name="%s[image]" value="%s">',
        esc_attr( self::$meta_background ),
        esc_url( $background['image'] )
    );
    ?>
</div>
