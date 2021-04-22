<div id="knife-analytics-options" class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Статистика просмотров', 'knife-theme'); ?></h1>

    <form method="get">
        <?php
            printf('<input type="hidden" name="page" value="%s">',
                esc_attr(self::$page_slug)
            );

            $table->display();
        ?>
    </form>
</div>
