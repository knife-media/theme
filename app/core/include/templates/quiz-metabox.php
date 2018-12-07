<div id="knife-quiz-box">
    <?php
        $post_id = get_the_ID();

        // Quiz options
        $options = get_post_meta($post_id, self::$meta_options, true);

        // Quiz items
        $items = get_post_meta($post_id, self::$meta_items);

        if(count($items) < 1) {
            array_push($items, ['caption' => '', 'description' => '']);
        }

        wp_nonce_field('metabox', self::$nonce);
    ?>

    <div class="box box--items">
        <div class="item">
            <div class="option option--manage">
                <span class="dashicons dashicons-menu"></span>
                <span class="dashicons dashicons-trash"></span>
            </div>

            <div class="option option--general">
                <div class="option__general">
                    <?php
                        printf('<textarea class="option__general-question wp-editor-area" name="%s[][question]">%s</textarea>',
                            esc_attr(self::$meta_items),
                            esc_attr($item['question'] ?? '')
                        );
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box box--actions">
        <?php
            printf('<button class="actions__add button" type="button">%s</button>',
                __('Добавить элемент', 'knife-theme')
            );
        ?>
    </div>
</div>
