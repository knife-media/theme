<div id="knife-seo-box">
    <?php
    // Get SEO fields from meta
    $fields = get_post_meta( get_the_ID(), self::$meta_seo, true );
    ?>

    <p>
        <strong><?php esc_html_e( 'Заголовок:', 'knife-theme' ); ?></strong>

        <?php
        printf(
            '<input name="%s[title]" type="text" value="%s" placeholder="%s">',
            esc_attr( self::$meta_seo ),
            esc_attr( $fields['title'] ?? '' ),
            esc_attr__( 'Название статьи с ключевым словом', 'knife-theme' )
        );
        ?>
    </p>

    <p>
        <strong><?php esc_html_e( 'Описание:', 'knife-theme' ); ?></strong>

        <?php
        printf(
            '<textarea name="%1$s[description]" placeholder="%3$s">%2$s</textarea>',
            esc_attr( self::$meta_seo ),
            esc_attr( $fields['description'] ?? '' ),
            esc_attr__( 'Краткое описание статьи с использованием ключевых слов', 'knife-theme' )
        );
        ?>
    </p>
</div>
