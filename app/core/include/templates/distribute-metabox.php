<div id="knife-distribute-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Distribute items
        $items = (array) get_post_meta($post_id, self::$meta_items, true);

        // Prepend empty array to items
        $items = array(0 => []) + $items;

        // Get availible channels
        $channels = [];

        foreach(KNIFE_DISTRIBUTE as $name => $settings) {
            if(isset($settings['label'], $settings['delivery'])) {
                $channels[$name] = $settings;
            }
        }
    ?>

    <div class="box box--items">

        <?php foreach($items as $uniqid => $item) : ?>
            <div class="item">
                <?php
                    $item = wp_parse_args((array) $item, [
                        'targets' => [],
                        'excerpt' => '',
                        'attachment' => ''
                    ]);

                    printf(
                        '<input class="item__uniqid" data-item="uniqid" type="hidden" value="%s">',
                        sanitize_title($uniqid)
                    );
                ?>

                <?php if(isset($item['sent'])) : ?>

                    <div class="item__targets">
                        <?php
                            foreach($item['targets'] as $target) {
                                if(empty($channels[$target])) {
                                    continue;
                                }

                                $label = $channels[$target]['label'];

                                if(intval($item['sent']) < 0) {
                                    printf(
                                        '<span class="item__targets-load" title="%s">%s</span>',
                                        __('Задача в обработке', 'knife-theme'),
                                        esc_html($label)
                                    );

                                    continue;
                                }

                                if(isset($item['complete'][$target], $channels[$target])) {
                                    printf(
                                        '<a class="item__targets-link" href="%s" target="_blank">%s</a>',
                                        esc_url($item['complete'][$target]),
                                        esc_html($label)
                                    );

                                    continue;
                                }

                                if(empty($item['errors'][$target])) {
                                    $item['errors'][$target] = __('Непредвиденная ошибка сервера', 'knife-theme');
                                }

                                printf(
                                    '<span class="item__targets-error" title="%s">%s</span>',
                                    esc_attr($item['errors'][$target]),
                                    esc_html($label)
                                );
                            }
                        ?>
                    </div>

                    <div class="item__snippet">
                        <?php
                            printf(
                                '<textarea class="item__snippet-excerpt" readonly>%s</textarea>',
                                sanitize_textarea_field($item['excerpt'])
                            );
                        ?>

                        <figure class="item__snippet-poster">
                            <?php
                                if(!empty($item['attachment'])) {
                                    printf('<img src="%s" alt="">',
                                        wp_get_attachment_image_url($item['attachment'])
                                    );
                                }
                            ?>
                        </figure>
                    </div>

                    <?php if(intval($item['sent']) >= 0) : ?>
                        <div class="item__sent">
                            <span class="dashicons dashicons-calendar"></span>

                            <?php
                                $sent = date('Y-m-d H:i:s', $item['sent']);

                                printf(
                                    '<span>%s</span><strong>%s</strong>',
                                    __('Отправлено:', 'knife-theme'),
                                    get_date_from_gmt($sent, 'd.m.Y G:i')
                                );
                            ?>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <?php
                        $scheduled = wp_next_scheduled('knife_schedule_distribution', [
                            sanitize_title($uniqid), absint($post_id)
                        ]);

                        $delay = strtotime("+ 15 minutes", current_time('timestamp'));
                    ?>

                    <?php if(count($channels) > 0) : ?>
                        <div class="item__targets">
                            <?php foreach($channels as $channel => $settings) : ?>
                                <label class="item__targets-check">
                                    <?php
                                        printf('<input type="checkbox" data-item="targets" data-delivery="%s" value="%s"%s>',
                                            esc_attr($settings['delivery']), esc_attr($channel),
                                            checked(in_array($channel, $item['targets']), true, false)
                                        );

                                        printf('<span>%s</span>', esc_html($settings['label']));
                                    ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="item__snippet">
                        <?php
                            printf(
                                '<textarea class="item__snippet-excerpt" data-item="excerpt" placeholder="%s">%s</textarea>',
                                __('Напишите подвдоку для соцсетей', 'knife-theme'),
                                sanitize_textarea_field($item['excerpt'])
                            );
                        ?>

                        <figure class="item__snippet-poster">
                            <?php
                                if(!empty($item['attachment'])) {
                                    printf('<img src="%s" alt="">',
                                        wp_get_attachment_image_url($item['attachment'])
                                    );
                                }
                            ?>

                            <?php
                                printf(
                                    '<input class="item__snippet-attachment" data-item="attachment" type="hidden" value="%s">',
                                    sanitize_text_field($item['attachment'])
                                );
                            ?>

                            <figcaption class="item__snippet-caption">+</figcaption>
                            <span class="item__snippet-delete dashicons dashicons-trash"></span>
                        </figure>

                        <span class="item__snippet-status"></span>
                    </div>


                    <?php if($scheduled) : ?>
                        <div class="item__scheduled">
                            <span class="dashicons dashicons-clock"></span>

                            <?php
                                $scheduled = date('Y-m-d H:i:s', $scheduled);

                                printf(
                                    '<span>%s</span><strong>%s</strong>',
                                    __('Запланировано на:', 'knife-theme'),
                                    get_date_from_gmt($scheduled, 'd.m.Y G:i')
                                );

                                printf(
                                    '<button class="item__scheduled-cancel button-link" type="button">%s</button>',
                                    __('Отменить', 'knife-theme')
                                );
                            ?>

                            <span class="spinner"></span>
                        </div>
                    <?php endif; ?>

                    <div class="item__delay">
                        <select class="item__delay-date" data-item="date">
                            <option value><?php _e('Не отправлять автоматически', 'knife-theme'); ?></option>
                            <option value="now"><?php _e('Отправить сразу', 'knife-theme'); ?></option>

                            <?php
                                for($i = 0; $i < 5; $i++) {
                                    $date = strtotime("+ $i days", current_time('timestamp'));

                                    printf('<option value="%s">%s</option>',
                                        date('Y-m-d', $date), date_i18n('j F, l', $date)
                                    );
                                }
                            ?>
                        </select>

                        <div class="item__delay-time">
                            <select class="item__delay-hour" data-item="hour">
                                <?php
                                    for($i = 0; $i < 24; $i++) {
                                        $hour = str_pad($i, 2, '0', STR_PAD_LEFT);

                                        printf('<option value="%1$s"%2$s>%1$s</option>',
                                            $hour, selected($hour, date('H', $delay), false)
                                        );
                                    }
                                ?>
                            </select>

                            <span class="item__delay-colon"><?php _e(':', 'knife-theme'); ?></span>

                            <select class="item__delay-minute" data-item="minute">
                                <?php
                                    for($i = 0; $i < 60; $i++) {
                                        $minute = str_pad($i, 2, '0', STR_PAD_LEFT);

                                        printf('<option value="%1$s"%2$s>%1$s</option>',
                                            $minute, selected($minute, date('i', $delay), false)
                                        );
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="item__manage">
                        <label class="item__manage-collapse">
                            <?php
                                printf('<input type="checkbox" data-item="collapse" value="1"%s><span>%s</span>',
                                    checked(empty($item['collapse']), false, false),
                                    __('Не формировать превью ссылки', 'knife-theme')
                                );
                            ?>
                        </label>
                    </div>

                    <span class="item__delete dashicons dashicons-trash" title="<?php _e('Удалить задачу', 'knife-theme'); ?>"></span>

                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="box box--actions">
        <div class="actions">
            <?php
                printf('<button class="actions__add button" type="button">%s</button>',
                    __('Добавить задачу', 'knife-theme')
                );
            ?>
        </div>
    </div>
</div>

<div class="hide-if-js">
    <p><?php _e('Эта функция требует JavaScript', 'knife-theme'); ?></p>
</div>

