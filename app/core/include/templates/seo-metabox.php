<div id="knife-seo-box">
    <?php
        $post_id = get_the_ID();

        // Get SEO fields from meta
        $fields = get_post_meta($post_id, self::$meta_seo, true);
    ?>

    <p>
        <strong><?php _e('Заголовок:', 'knife-theme'); ?></strong>

        <?php
            printf(
                '<input name="%s[title]" type="text" value="%s" placeholder="%s">',
                esc_attr(self::$meta_seo), esc_attr($fields['title'] ?? ''),
                __('Название статьи с ключевым словом', 'knife-theme')
            );
        ?>
    </p>

    <p>
        <strong><?php _e('Описание:', 'knife-theme'); ?></strong>

        <?php
            printf(
                '<textarea name="%1$s[description]" placeholder="%3$s">%2$s</textarea>',
                esc_attr(self::$meta_seo), esc_attr($fields['description'] ?? ''),
                __('Краткое описание статьи с использованием ключевых слов', 'knife-theme')
            );
        ?>
    </p>
</div>