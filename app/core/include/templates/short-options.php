<div class="wrap">
    <?php
        include_once($include . '/tables/short-links.php');

        // Get short links table instance
        $table = new Knife_Short_Links_Table(self::$short_db);
    ?>
    <h1><?php _e('Сокращатель ссылок', 'knife-theme'); ?></h1>

    <hr class="wp-header-end" />

    <?php settings_errors(); ?>

    <form method="post" class="wp-privacy-request-form">
        <h2><?php esc_html_e( 'Add Data Export Request' ); ?></h2>
        <p><?php esc_html_e( 'An email will be sent to the user at this email address asking them to verify the request.' ); ?></p>

        <div class="wp-privacy-request-form-field">
            <label for="username_or_email_for_privacy_request"><?php esc_html_e( 'Username or email address' ); ?></label>
            <input type="text" required class="regular-text" id="username_or_email_for_privacy_request" name="username_or_email_for_privacy_request" />
            <?php submit_button( __( 'Send Request' ), 'secondary', 'submit', false ); ?>
        </div>
        <?php wp_nonce_field( 'personal-data-request' ); ?>
        <input type="hidden" name="action" value="add_export_personal_data_request" />
        <input type="hidden" name="type_of_action" value="export_personal_data" />
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
