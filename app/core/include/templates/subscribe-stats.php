<div id="knife-subscribe-options" class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Статистика переходов', 'knife-theme' ); ?>
    </h1>

    <?php
    printf(
        '<a href="%2$s" class="page-title-action">%1$s</a>',
        esc_html__( 'Вернуться к рассылкам', 'knife-theme' ),
        esc_url( admin_url( '/tools.php?page=' . self::$page_slug ) )
    );

    printf(
        '<h4 class="sub-heading">%s «%s»</h4>',
        esc_html__( 'Рассылка:', 'knife-theme' ),
        esc_html( $prepared['title'] )
    );
    ?>

    <div class="analytics">
        <dl>
            <?php
            foreach ( $analytics as $row ) {
                printf(
                    '<dt><a href="%1$s" target="_blank" rel="noopener">%1$s</a></dt><dd>%2$d</dd>',
                    esc_url( $row['reference'] ),
                    absint( $row['amount'] )
                );
            }
            ?>
        </dl>
    </div>
</div>
