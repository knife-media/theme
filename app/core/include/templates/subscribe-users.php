<div id="knife-subscribe-options" class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Список подписчиков', 'knife-theme' ); ?>
    </h1>

    <?php
    printf(
        '<a href="%2$s" class="page-title-action">%1$s</a>',
        esc_html__( 'Вернуться к рассылкам', 'knife-theme' ),
        esc_url( admin_url( '/tools.php?page=' . self::$page_slug ) )
    );
    ?>

    <hr class="wp-header-end">

    <?php settings_errors( 'knife-subscribe-actions' ); ?>

    <form class="searching wp-clearfix">
        <?php
        $table->search_box( esc_html__( 'Найти пользователей', 'knife-theme' ), self::$page_slug );

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

        <input type="hidden" name="tab" value="users">
    </form>

    <form method="post" class="users">
        <?php $table->display(); ?>
    </form>
</div>
