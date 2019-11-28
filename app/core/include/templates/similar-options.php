<div id="knife-similar-options" class="wrap">
    <h1><?php _e('Настройки промо блока рекомендаций', 'knife-theme'); ?></h1>

    <form method="post" class="form-field">
        <?php
            printf(
                '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="title" required><small>%3$s</small></p>',
                esc_attr(self::$settings_slug . '-title'),
                __('Текст ссылки на промо материал:', 'knife-theme'),
                __('Допустимо использование тега &lt;em&gt;', 'knife-theme')
            );

            printf(
                '<p><label for="%1$s">%2$s</label><input type="text" id="%1$s" name="link" required></p>',
                esc_attr(self::$settings_slug . '-link'),
                __('Адрес ссылки:', 'knife-theme')
            );

            wp_nonce_field('knife-similar-append');
            submit_button(__('Добавить ссылку', 'knife-theme'), 'primary', 'submit', false);
        ?>

        <input type="hidden" name="action" value="append">
    </form>

    <div class="form-items">
        <?php
            $promo = get_option(self::$option_promo, []);

            // Get current page admin link
            $admin_url = admin_url('/options-general.php?page=' . self::$settings_slug);
        ?>

        <?php foreach($promo as $i => $item) : ?>
            <div class="item">
                <?php
                    printf('<strong class="item-title">%s</strong>',
                        esc_html($item['title'])
                    );

                    printf('<a class="item-link" href="%1$s" target="_blank">%1$s</a>',
                        esc_url($item['link'])
                    );

                    $delete_args = [
                        'action' => 'delete',
                        'id' => $i
                    ];

                    $delete_link = add_query_arg($delete_args, $admin_url);

                    printf('<a class="item-delete dashicons dashicons-trash" href="%s"></a>',
                        esc_url(wp_nonce_url($delete_link, 'knife-similar-delete'))
                    );
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
