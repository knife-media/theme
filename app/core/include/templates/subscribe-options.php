<div id="knife-subscribe-options" class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Менеджер рассылок', 'knife-theme' ); ?>
    </h1>

    <?php
    printf(
        '<a href="%2$s" class="page-title-action">%1$s</a>',
        esc_html__( 'Создать новую', 'knife-theme' ),
        esc_url( admin_url( '/tools.php?page=' . self::$page_slug ) )
    );
    ?>

    <hr class="wp-header-end">

    <?php settings_errors( 'knife-subscribe-actions' ); ?>

    <form class="content" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <div class="screen">
            <div class="screen-fields">
                <div class="fields-title">
                    <?php
                    printf(
                        '<input type="text" id="%s" name="title" placeholder="%s" value="%s" required>',
                        esc_attr( self::$page_slug . '-title' ),
                        esc_attr__( 'Тема письма', 'knife-theme' ),
                        esc_html( $prepared['title'] )
                    );
                    ?>
                </div>

                <div class="fields-content">
                    <?php
                    wp_editor(
                        $prepared['content'],
                        self::$page_slug . '-content',
                        array(
                            'wpautop'          => true,
                            'media_buttons'    => false,
                            'textarea_id'      => esc_attr( self::$page_slug . '-content' ),
                            'textarea_name'    => 'content',
                            'textarea_rows'    => 20,
                            'tinymce'          => array(
                                'toolbar1'      => 'formatselect,link,bold,italic,hrlink,unlink',
                                'toolbar2'      => false,
                                'block_formats' => 'Paragraph=p;Heading 2=h2;Heading 3=h3;',
                            ),
                            'quicktags'        => false,
                            'drag_drop_upload' => false,
                        )
                    );
                    ?>
                </div>
            </div>

            <aside class="screen-aside">
                <figure>
                    <h2><?php esc_html_e( 'Сводная статистика', 'knife-theme' ); ?></h2>

                    <dl>
                        <?php
                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Активных пользоваталей:', 'knife-theme' ),
                            absint( $summary['active'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Всего пользователей:', 'knife-theme' ),
                            absint( $summary['total'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Подписок за неделю:', 'knife-theme' ),
                            absint( $summary['new_week'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Подписок за месяц:', 'knife-theme' ),
                            absint( $summary['new_month'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Отписок за неделю:', 'knife-theme' ),
                            absint( $summary['left_week'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s</dd>',
                            esc_html__( 'Отписок за месяц:', 'knife-theme' ),
                            absint( $summary['left_month'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s%%</dd>',
                            esc_html__( 'Процент прочтений:', 'knife-theme' ),
                            absint( $summary['avg_open'] )
                        );

                        printf(
                            '<dt>%s</dt><dd>%s%%</dd>',
                            esc_html__( 'Процент переходов:', 'knife-theme' ),
                            absint( $summary['avg_click'] )
                        );
                        ?>
                    </dl>

                    <hr>

                    <?php
                    printf(
                        '<a href="%2$s" class="button">%1$s</a>',
                        esc_html__( 'Список подписчиков', 'knife-theme' ),
                        esc_url( add_query_arg( array( 'tab' => 'users' ), admin_url( '/tools.php?page=' . self::$page_slug ) ) )
                    );
                    ?>
                </figure>
            </aside>
        </div>

        <div class="manager">
            <?php
            printf(
                '<input type="hidden" name="action" value="%s">',
                esc_attr( self::$page_slug . '-submit' )
            );

            wp_nonce_field( 'knife-subscribe-submit' );
            submit_button( esc_html__( 'Сохранить черновик', 'knife-theme' ), 'secondary', 'save', false );

            if ( ! empty( $prepared['id'] ) ) {
                printf(
                    '<input type="hidden" name="id" value="%d">',
                    absint( $prepared['id'] )
                );

                $demolink = null;

                if ( isset( KNIFE_SUBSCRIBE['url'] ) ) {
                    $demolink = sprintf( rtrim( KNIFE_SUBSCRIBE['url'], '/' ) . '/preview/%d', absint( $prepared['id'] ) );
                }

                printf(
                    '<a class="button" href="%2$s" target="_blank" rel="noopener">%1$s</a>',
                    esc_html__( 'Просмотреть', 'knife-theme' ),
                    esc_url( $demolink )
                );

                printf(
                    '<input type="datetime-local" name="released" value="%s">',
                    esc_attr( wp_date( 'Y-m-d H:i', strtotime( '+30 minutes', time() ) ) )
                );

                submit_button( esc_html__( 'Запланировать', 'knife-theme' ), 'primary', 'schedule', false );
            }
            ?>
        </div>
    </form>

    <hr>

    <form method="get" class="searching wp-clearfix">
        <?php
        $table->search_box( esc_html__( 'Найти рассылки', 'knife-theme' ), self::$page_slug );

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

    <form method="post" class="listing">
        <?php
        printf(
            '<input type="hidden" name="page" value="%s">',
            esc_attr( self::$page_slug )
        );

        $table->display();
        ?>
    </form>
</div>
