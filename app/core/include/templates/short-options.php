<div id="knife-short-options" class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Сокращатель ссылок', 'knife-theme'); ?></h1>

    <?php settings_errors('knife-short-actions'); ?>

    <form method="post" class="form-field">
        <p>
            <label for="knife-short-url"><?php _e('Полный URL длинной ссылки', 'knife-theme'); ?></label>
            <input type="text" id="knife-short-url" name="url" required>
        </p>

        <p>
            <label for="knife-short-keyword"><?php _e('Адрес короткой ссылки', 'knife-theme'); ?></label>
            <input type="text" id="knife-short-keyword" name="keyword" required>
        </p>

        <input type="hidden" name="action" value="knife-short-append" />

        <?php
            wp_nonce_field('knife-short-append');
            submit_button(__('Сократить ссылку', 'knife-theme'), 'primary', 'submit', false);
        ?>
    </form>

    <hr />

    <form class="search-form wp-clearfix">
        <?php
            $table->search_box(__('Найти ссылки', 'knife-theme'), 'knife-short');

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
