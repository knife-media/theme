<div id="knife-short-options" class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Сокращатель ссылок', 'knife-theme'); ?></h1>

    <?php settings_errors('knife-short-actions'); ?>

    <form method="post" class="form-field">
        <?php
            printf(
                '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="url" required></p>',
                esc_attr(self::$page_slug . '-url'),
                __('Полный URL длинной ссылки', 'knife-theme')
            );

            printf(
                '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="keyword" required></p>',
                esc_attr(self::$page_slug . '-keyword'),
                __('Адрес короткой ссылки', 'knife-theme')
            );

            printf('<input type="hidden" name="action" value="%s">',
                esc_attr(self::$page_slug . '-append')
            );

            wp_nonce_field('knife-short-append');
            submit_button(__('Сократить ссылку', 'knife-theme'), 'primary', 'submit', false);
        ?>
    </form>

    <hr />

    <form class="search-form wp-clearfix">
        <?php
            $table->search_box(__('Найти ссылки', 'knife-theme'), self::$page_slug);

            printf('<input type="hidden" name="page" value="%s">',
                esc_attr(self::$page_slug)
            );

            printf('<input type="hidden" name="orderby" value="%s">',
                esc_attr(sanitize_text_field($_REQUEST['orderby'] ?? ''))
            );

            printf('<input type="hidden" name="order" value="%s">',
                esc_attr(sanitize_text_field($_REQUEST['order'] ?? ''))
            );
        ?>
    </form>

    <form method="post">
        <?php $table->display(); ?>
    </form>
</div>
