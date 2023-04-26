<div id="knife-promo-box">
    <?php
    // Check if post promoted
    $promo = get_post_meta( get_the_ID(), self::$meta_promo, true );

    // Get post teaser
    $teaser = get_post_meta( get_the_ID(), self::$meta_teaser, true );

    // Get post pixel
    $pixel = get_post_meta( get_the_ID(), self::$meta_pixel, true );

    // Get post ORD
    $ord = get_post_meta( get_the_ID(), self::$meta_ord, true );

    // Get promo options
    $options = get_post_meta( get_the_ID(), self::$meta_options, true );

    printf(
        '<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
        esc_attr( self::$meta_promo ),
        esc_html__( 'Партнерский материал', 'knife-theme' ),
        checked( $promo, 1, false )
    );
    ?>

    <div class="promo-advert hidden">
        <?php
        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[link]" value="%2$s"></p>',
            esc_attr( self::$meta_options ),
            esc_url( $options['link'] ?? '' ),
            esc_attr__( 'Ссылка с плашки:', 'knife-theme' )
        );

        printf(
            '<p><strong>%2$s</strong><input class="logo widefat" type="text" name="%1$s[logo]" value="%3$s">%4$s</p>',
            esc_attr( self::$meta_options ),
            esc_html__( 'Ссылка на логотип:', 'knife-theme' ),
            esc_url( $options['logo'] ?? '' ),
            sprintf(
                '<small><a class="upload" href="%s" target="_blank">%s</a></small>',
                esc_url( admin_url( '/media-new.php' ) ),
                esc_html__( 'Загрузить изображение', 'knife-theme' )
            )
        );

        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[title]" value="%2$s"></p>',
            esc_attr( self::$meta_options ),
            esc_attr( $options['title'] ?? '' ),
            esc_attr__( 'Рекламодатель:', 'knife-theme' )
        );

        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s[text]" value="%2$s"></p>',
            esc_attr( self::$meta_options ),
            esc_attr( $options['text'] ?? esc_html__( 'Партнерский материал', 'knife-theme' ) ),
            esc_attr__( 'Тип материала:', 'knife-theme' )
        );
        ?>

        <div>
        <?php
            printf(
                '<strong>%s</strong>',
                esc_html__( 'Цвет плашки:', 'knife-theme' )
            );

            printf(
                '<input class="color-picker" name="%s[color]" type="text" value="%s">',
                esc_attr( self::$meta_options ),
                esc_attr( $options['color'] ?? '' ),
            );
            ?>
        </div>
    </div>

    <div class="promo-teaser">
        <?php
        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
            esc_attr( self::$meta_teaser ),
            esc_attr( $teaser ),
            esc_html__( 'Пользовательская ссылка:', 'knife-theme' )
        );
        ?>
    </div>

    <div class="promo-pixel">
        <?php
        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
            esc_attr( self::$meta_pixel ),
            esc_attr( $pixel ),
            esc_html__( 'Пиксель для подсчета охвата:', 'knife-theme' )
        );
        ?>

        <?php
        printf(
            '<p><strong>%3$s</strong><input class="widefat" type="text" name="%1$s" value="%2$s"></p>',
            esc_attr( self::$meta_ord ),
            esc_attr( $ord ),
            esc_html__( 'Пиксель для ОРД:', 'knife-theme' )
        );
        ?>
    </div>
</div>
