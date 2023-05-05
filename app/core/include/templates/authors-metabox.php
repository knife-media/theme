<div id="knife-authors-box" class="hide-if-no-js">
    <?php
    // Get post authors meta
    $authors = self::get_post_authors( get_the_ID() );

    // Get post editor meta
    $editor = get_post_meta( get_the_ID(), self::$meta_editor, true );
    ?>

    <?php
    printf(
        '<p class="editors-title"><strong>%s</strong></p>',
        esc_html__( 'Редактор материала', 'knife-theme' )
    );
    ?>

    <select class="editors-select" name="<?php echo esc_attr( self::$meta_editor ); ?>">
        <?php
        printf(
            '<option name="">%s</option>',
            esc_html__( 'Без редактора', 'knife-theme' )
        );

        foreach ( self::get_editors() as $name => $caption ) {
            printf(
                '<option value="%1$s"%3$s>%2$s</option>',
                esc_attr( $name ),
                esc_html( $caption ),
                selected( $editor, $name, false )
            );
        }
        ?>
    </select>

    <hr>

    <?php
    printf(
        '<input class="authors-input" type="text" placeholder="%s">',
        esc_html__( 'Поиск автора', 'knife-theme' )
    );

    foreach ( $authors as $author ) {
        $user = get_userdata( $author );

        printf(
            '<p class="authors-item">%s<span class="authors-delete"></span>%s</p>',
            sprintf(
                '<input type="hidden" name="%s[]" value="%s">',
                esc_attr( self::$meta_authors ),
                esc_attr( $user->ID )
            ),
            esc_html( $user->display_name )
        );
    }
    ?>
</div>
