<div id="knife-views-options" class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Сокращатель ссылок', 'knife-theme'); ?></h1>

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
