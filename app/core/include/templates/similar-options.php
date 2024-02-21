<div id="knife-similar-options" class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Настройки промо блока рекомендаций', 'knife-theme' ); ?></h1>

    <?php settings_errors( 'knife-similar-actions' ); ?>

    <form method="post" class="form-field" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php
        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="title" required><small>%3$s</small></p>',
            esc_attr( self::$page_slug . '-title' ),
            esc_html__( 'Текст ссылки на промо материал:', 'knife-theme' ),
            esc_html__( 'Допустимо использование тега &lt;em&gt;', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="link" required></p>',
            esc_attr( self::$page_slug . '-link' ),
            esc_html__( 'Адрес ссылки:', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="pixel"><small>%3$s</small></p>',
            esc_attr( self::$page_slug . '-pixel' ),
            esc_html__( 'Адрес пикселя:', 'knife-theme' ),
            esc_html__( 'Необязательное поле. Ожидается ссылка на изображение', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="erid"><small>%3$s</small></p>',
            esc_attr( self::$page_slug . '-pixel' ),
            esc_html__( 'Код ERID:', 'knife-theme' ),
            esc_html__( 'Необязательное поле', 'knife-theme' )
        );

        printf(
            '<input type="hidden" name="action" value="%s">',
            esc_attr( self::$page_slug . '-append' )
        );

        wp_nonce_field( 'knife-similar-append' );
        submit_button( esc_html__( 'Добавить ссылку', 'knife-theme' ), 'primary', 'submit', false );
        ?>
    </form>

    <div class="form-items">
        <?php
        $promo = get_option( self::$option_promo, array() );

        // Get current page admin link
        $admin_url = menu_page_url( self::$page_slug, false );
        ?>

        <?php foreach ( $promo as $i => $item ) : ?>
            <div class="item">
                <?php
                printf(
                    '<strong class="item-title">%s</strong>',
                    esc_html( $item['title'] )
                );

                printf(
                    '<a class="item-link" href="%1$s" target="_blank">%1$s</a>',
                    esc_url( $item['link'] )
                );

                if ( ! empty( $item['pixel'] ) ) {
                    printf(
                        '<span class="item-pixel">%s</span>',
                        esc_url( $item['pixel'] )
                    );
                }

                if ( ! empty( $item['erid'] ) ) {
                    printf(
                        '<span class="item-erid">%s</span>',
                        esc_html( $item['erid'] )
                    );
                }

                $delete_args = array(
                    'action' => 'delete',
                    'id'     => $i,
                );

                $delete_link = add_query_arg( $delete_args, $admin_url );

                printf(
                    '<a class="item-delete dashicons dashicons-trash" href="%s" onclick="%s"></a>',
                    esc_url( wp_nonce_url( $delete_link, 'knife-similar-delete' ) ),
                    sprintf(
                        "return confirm('%s')",
                        esc_html__( 'Уверены, что хотите удалить ссылку?', 'knife-theme' )
                    )
                );
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
