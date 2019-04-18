<div id="knife-distribute-box">
    <div class="snippet">
        <div class="snippet__network">
            <?php
                $networks = [
                    'facebook_1' => 'Facebook',
                    'vk_1' => 'Вконтакте',
                    'vk_2' => 'Вконтакте: Тупой нож',
                    'twitter' => 'Twitter',
                    'telegram_1' => 'Telegram',
                    'telegram_2' => 'Telegram: Тупой нож'
                ];

                foreach($networks as $network => $label) {
                    printf('<label class="snippet__network-item"><input type="checkbox" name="%s" value="1"><span>%s</span></label>',
                        esc_attr($network),
                        esc_html($label)
                    );
                }
            ?>
        </div>

        <div class="snippet__text">
            <?php
                printf(
                    '<textarea class="snippet__text-excerpt" name="" placeholder="%2$s">%1$s</textarea>',
                    wp_kses_post($result['description'] ?? ''),
                    __('Напишите подвдоку для соцсетей', 'knife-theme')
                );
            ?>

            <figure class="snippet__text-poster">
                <figcaption class="snippet__text-caption">+</figcaption>

                <?php
                    printf(
                        '<input class="snippet__text-attachment" type="hidden" data-result="attachment" value="%s">',
                        sanitize_text_field($result['attachment'] ?? '')
                    );
                ?>
            </figure>
        </div>


        <div class="snippet__time">
            <?php
                $timing = [
                    '1' => __('Опубликовать cразу', 'knife-theme'),
                    '5' => __('Через 5 минут', 'knife-theme'),
                    '15' => __('Через 15 минут','knife-theme'),
                    '30' => __('Через 30 минут','knife-theme'),
                    '60' => __('Через час','knife-theme')
                ];

                $select = [];

                foreach($timing as $delay => $label) {
                    $select[] = sprintf('<option value="%s">%s</option>',
                        absint($delay), esc_html($label)
                    );
                }

                printf('<select class="snippet__time-select" name="">%s</select>',
                    implode('', $select)
                );

                if(get_post_status(get_the_ID()) === 'draft') {
                    printf('<p class="snippet__time-howto howto">%s</p>',
                        __('с момента выхода поста', 'knife-theme')
                    );
                }
            ?>
        </div>

        <span class="snippet__delete dashicons dashicons-trash" title="<?php _e('Удалить задачу', 'knife-theme'); ?>"></span>
    </div>

    <div class="actions">
        <?php
            printf('<button class="actions__add button" type="button">%s</button>',
                __('Добавить задачу', 'knife-theme')
            );
        ?>
    </div>
</div>
