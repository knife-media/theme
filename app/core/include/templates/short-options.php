<div id="knife-short-options" class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Сокращатель ссылок', 'knife-theme' ); ?></h1>

    <?php settings_errors( 'knife-short-actions' ); ?>

    <form method="post" class="form-field" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php
        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="url" required></p>',
            esc_attr( self::$page_slug . '-url' ),
            esc_attr__( 'Полный URL длинной ссылки', 'knife-theme' )
        );

        printf(
            '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="keyword" required></p>',
            esc_attr( self::$page_slug . '-keyword' ),
            esc_attr__( 'Адрес короткой ссылки', 'knife-theme' )
        );

        $options = array();

        foreach ( self::$hosts as $host ) {
            $options[] = sprintf( '<option value="%1$s">%1$s</option>', $host );
        }

        printf(
            '<p><label for="%1$s">%2$s</label><select id="%1$s" name="host">%3$s</select></p>',
            esc_attr( self::$page_slug . '-host' ),
            esc_html__( 'Хост сокращалки', 'knife-theme' ),
            implode( '', $options ) // phpcs:ignore
        );

        printf(
            '<input type="hidden" name="action" value="%s">',
            esc_attr( self::$page_slug . '-append' )
        );

        wp_nonce_field( 'knife-short-append' );
        submit_button( esc_html__( 'Сократить ссылку', 'knife-theme' ), 'primary', 'submit', false );
        ?>
    </form>

    <hr />

    <form class="search-form wp-clearfix">
        <?php
        $table->search_box( esc_html__( 'Найти ссылки', 'knife-theme' ), self::$page_slug );

        printf(
            '<input type="hidden" name="page" value="%s">',
            esc_attr( self::$page_slug )
        );

        printf(
            '<input type="hidden" name="orderby" value="%s">',
            esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ?? '' ) ) )
        );

        printf(
            '<input type="hidden" name="order" value="%s">',
            esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['order'] ?? '' ) ) )
        );
        ?>
    </form>

    <form method="post">
        <?php $table->display(); ?>
    </form>
</div>
