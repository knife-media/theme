<div id="knife-story-box">
    <?php
        // get stories array
        $post_id = get_the_ID();
        $stories = get_post_meta($post_id, $this->meta . '-stories');

        $options = [];

        // get options
        foreach(['background', 'shadow', 'effect'] as $item) {
            $options[$item] = get_post_meta($post_id, $this->meta . "-{$item}", true);
        }

        if(count($stories) < 1) {
            $stories[] = ['text' => '', 'image' => ''];
        }
    ?>


    <div class="box box--items">
    <?php foreach($stories as $i => $story) : ?>
        <div class="item">
            <?php
                if(!empty($story['image'])) {
                    printf('<img class="item__image" src="%s" alt="">', esc_url($story['image']));
                }


                printf('<textarea data-form="text" class="item__text" name="%1$s">%2$s</textarea>',
                    $this->meta . '-stories[][text]',
                    $story['text'] ?? ''
                );


                printf('<input data-form="image" type="hidden" name="%s" value="%s">',
                    $this->meta . '-stories[][image]',
                    $story['image'] ?? ''
                );
            ?>

            <div class="item__field">
                <span class="item__field-drag"></span>
                <span class="item__field-image" title="<?php _e('Добавить изображение', 'knife-theme'); ?>"></span>
                <span class="item__field-trash" title="<?php _e('Удалить слайд', 'knife-theme'); ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="box box--actions">
        <button class="actions__add button"><?php _e('Добавить слайд в историю'); ?></button>
    </div>

    <div class="box box--options">
        <div class="option option--background">
            <figure class="option__background">
                <?php if(!empty($options['background'])) : ?>
                    <img class="option__background-image" src="<?php echo $options['background']; ?>" alt="">
                <?php endif; ?>

                <figcaption class="option__background-blank"><?php _e('Выбрать изображение', 'knife-theme'); ?></figcaption>

                <?php
                    printf('<input data-form="background" type="hidden" name="%s" value="%s">',
                        $this->meta . '-background',
                        $options['background']
                    );
                ?>
            </figure>
        </div>

        <div class="option option--settings">
            <div class="option__shadow">
                <label class="option__label"><?php _e('Затемнение фона', 'knife-theme'); ?></label>

                <?php
                    printf('<input class="option__range" type="range" name="%1$s" min="0" max="100" step="5" value="%2$s">',
                        $this->meta . '-shadow',
                        absint($options['shadow'])
                    );
                ?>
            </div>

            <div class="option__effect">
                <label class="option__label"><?php _e('Эффект слайдера', 'knife-theme'); ?></label>

                <select class="option__select" name="<?php echo $this->meta . '-effect'; ?>">
                    <?php
                        $effects = [
                            'parallax' => __('Параллакс', 'knife-theme'),
                            'progress' => __('Прогресс', 'knife-theme'),
                            'coube' => __('Кубический', 'knife-theme')
                        ];

                        foreach($effects as $value => $title) {
                            printf('<option value="%1$s" %3$s>%2$s</option>',
                                $value, $title,
                                selected($options['effect'], $value)
                            );
                        }
                    ?>
                </select>
            </div>

        </div>
    </div>

</div>
