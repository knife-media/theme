<div id="knife-feed-box">
    <?php
    $zen_publish = get_post_meta( get_the_ID(), self::$zen_publish, true );
    $zen_exclude = get_post_meta( get_the_ID(), self::$zen_exclude, true );

    $zen_date = get_the_date( 'd.m.Y G:i', get_the_ID() );

    if ( $zen_publish ) {
        $zen_date = fmdate( 'd.m.Y G:i', strtotime( $zen_publish ) );
    }

    printf(
        '<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
        esc_attr( self::$zen_exclude ),
        esc_html__( 'Исключить из Яндекс.Дзен', 'knife-theme' ),
        checked( $zen_exclude, 1, false )
    );
    ?>

    <div style="line-height: 20px;">
        <span>
            <span class="dashicons dashicons-calendar" style="color:#82878c"></span>
            <?php esc_html_e( 'Републикация:', 'knife-theme' ); ?>
        </span>

        <?php
            printf(
                '<b id="knife-feed-display" class="publish-time" style="cursor: pointer;">%s</b>',
                esc_html( $zen_date )
            );
            ?>
        </b>
    </div>

    <?php
    printf(
        '<input id="knife-feed-publish" type="hidden" name="%s" value="%s">',
        esc_attr( self::$zen_publish ),
        esc_attr( $zen_publish )
    );
    ?>

    <div style="display: flex; align-items: center; margin-top: 10px;">
        <?php
        printf(
            '<a href="#current-time" class="button" data-display="%s" data-publish="%s">%s</a>',
            esc_html( date_i18n( 'd.m.Y G:i' ) ),
            esc_html( date_i18n( 'Y-m-d H:i:s' ) ),
            esc_html__( 'Текущее время', 'knife-theme' )
        );

        printf(
            '<a href="#reset-time" style="margin-left: 10px;" data-display="%s" data-publish="">%s</a>',
            get_the_date( 'd.m.Y G:i', get_the_ID() ),
            esc_html__( 'Сбросить', 'knife-theme' )
        );
        ?>
    </div>
</div>
