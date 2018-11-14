<div id="knife-zen-box">
    <?php
        $exclude = get_post_meta(get_the_ID(), self::$meta_exclude, true);
        $publish = get_post_meta(get_the_ID(), self::$meta_publish, true);

        $zendate = get_the_date("Y-m-d H:i:s", get_the_ID());

        if(strlen($publish) > 0) {
            $zendate = get_date_from_gmt($publish);
        }

        printf(
            '<p><label><input type="checkbox" name="%1$s" class="checkbox"%3$s> %2$s</label></p>',
            esc_attr(self::$meta_exclude),
            __('Исключить запись из ленты', 'knife-theme'),
            checked($exclude, 1, false)
        );
    ?>

    <div style="line-height: 20px;">
        <span>
            <span class="dashicons dashicons-calendar" style="color:#82878c"></span>
            <?php _e('Републикация:', 'knife-theme'); ?>
        </span>

        <b id="knife-zen-display" class="publish-time">
            <?php echo date("d.m.Y G:i", strtotime($zendate)); ?>
        </b>
    </div>

    <div style="display: flex; align-items: center; margin-top: 10px;">
        <?php
            printf(
                '<a href="#current-time" class="button" data-display="%s" data-publish="%s">%s</a>',
                date_i18n("d.m.Y G:i"),
                current_time('mysql', 1),
                __('Текущее время', 'knife-theme')
            );

            printf(
                '<a href="#reset-time" style="margin-left: 10px;" data-display="%s" data-publish="">%s</a>',
                get_the_date("d.m.Y G:i", get_the_ID()),
                __('Сбросить', 'knife-theme')
            );
        ?>
    </div>

    <?php
        printf(
            '<input id="knife-zen-publish" type="hidden" name="%1$s" value="%2$s">',
            esc_attr(self::$meta_publish),
            esc_attr($publish)
        );

        wp_nonce_field('fieldset', self::$nonce);
    ?>
</div>

