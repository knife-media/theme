<div id="knife-distribute-box" class="hide-if-no-js">
    <?php
        $post_id = get_the_ID();

        // Distribute items
        $items = get_post_meta($post_id, self::$meta_items);

        // Add empty item
        array_unshift($items, []);

        // Get availible channels
        $channels = [];

        foreach(KNIFE_DISTRIBUTE as $name => $settings) {
            if(!empty($settings['label'])) {
                $channels[$name] = sanitize_text_field($settings['label']);
            }
        }

        wp_nonce_field('metabox', self::$metabox_nonce);
    ?>

    <div class="box box--items">

        <?php foreach($items as $item) : ?>
            <div class="item">
                <?php
                    $item = wp_parse_args($item, [
                        'uniqid' => '',
                        'network' => [],
                        'excerpt' => '',
                        'delay' => 0,
                        'attachment' => ''
                    ]);

                    printf(
                        '<input class="item__uniqid" data-item="uniqid" type="hidden" value="%s">',
                        sanitize_title($item['uniqid'])
                    );

                    $scheduled = wp_next_scheduled('knife_schedule_distribution', [
                        sanitize_title($item['uniqid']), absint($post_id)
                    ]);
                ?>

                <div class="item__network">
                    <?php
                        foreach($channels as $channel => $label) {
                            printf('<label class="item__network-check">%s<span>%s</span></label>',
                                sprintf('<input type="checkbox" data-item="network" value="%s"%s>',
                                    esc_attr($channel),
                                    checked(in_array($channel, $item['network']), true, false)
                                ),

                                esc_html($label)
                            );
                        }
                    ?>
                </div>

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
                </div>

                <?php if(is_numeric($scheduled)) : ?>
                    <div class="item__schedule">
                        <span class="dashicons dashicons-calendar"></span>

                        <?php
                            $scheduled = date('Y-m-d H:i:s', $scheduled);

                            printf(
                                '<span>%s</span><strong>%s</strong>',
                                __('Задача запланирована на:', 'knife-theme'),
                                get_date_from_gmt($scheduled, 'd.m.Y G:i')
                            );

                            printf(
                                '<button class="item__schedule-cancel button-link" type="button">%s</button>',
                                __('Отменить', 'knife-theme')
                            );
                        ?>

                        <span class="spinner"></span>
                    </div>
                <?php endif; ?>

                <div class="item__delay">
                    <?php
                        $timing = [
                            '0' => __('Не отправлять автоматически', 'knife-theme'),
                            '1' => __('Отправить в соцсети cразу', 'knife-theme'),
                            '5' => __('Через 5 минут', 'knife-theme'),
                            '15' => __('Через 15 минут','knife-theme'),
                            '30' => __('Через 30 минут','knife-theme'),
                            '60' => __('Через час','knife-theme')
                        ];

                        $options = [];

                        foreach($timing as $delay => $label) {
                            $options[] = sprintf('<option value="%1$s"%3$s>%2$s</option>',
                                absint($delay), esc_html($label),
                                selected($item['delay'], $delay, false)
                            );
                        }

                        printf('<select class="item__delay-select" data-item="delay">%s</select>',
                            implode('', $options)
                        );

                        if(get_post_status(get_the_ID()) !== 'publish') {
                            printf('<p class="item__delay-howto howto">%s</p>',
                                __('после публикации поста', 'knife-theme')
                            );
                        }
                    ?>
                </div>

                <span class="item__delete dashicons dashicons-trash" title="<?php _e('Удалить задачу', 'knife-theme'); ?>"></span>
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
