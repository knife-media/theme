<div id="knife-story-box">
    <?php
        // get stories array
        $post_id = get_the_ID();
        $stories = get_post_meta($post_id, $this->meta . '-stories');

        $options = [];

        // get options
        foreach($this->opts as $item) {
            $options[$item] = get_post_meta($post_id, $this->meta . "-{$item}", true);
        }

        if(count($stories) < 1) {
            $stories[] = ['entry' => '', 'media' => ''];
        }

    ?>


    <div class="box box--items">
    <?php foreach($stories as $i => $story) : ?>
        <div class="item">
            <?php
                if(!empty($story['media'])) {
                    printf('<img class="item__image" src="%s" alt="">', wp_get_attachment_thumb_url($story['media']));
                }

                printf('<input class="item__media" type="hidden" name="%1$s" value="%2$s">',
                    $this->meta . '-stories[][media]', $story['media'] ?? ''
                );

                printf('<textarea class="item__entry" name="%1$s">%2$s</textarea>',
                    $this->meta . '-stories[][entry]', $story['entry'] ?? ''
                );
            ?>

            <div class="item__field">
                <span class="item__field-drag"></span>
                <span class="item__field-image" title="<?php _e('Добавить медиафайл', 'knife-theme'); ?>"></span>
                <span class="item__field-clear" title="<?php _e('Удалить медиафайл', 'knife-theme'); ?>"></span>

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
                    printf('<input class="option__background-media" type="hidden" name="%s" value="%s">',
                        $this->meta . '-background',
                        $options['background']
                    );
                ?>
            </figure>
        </div>

        <div class="option option--settings">
            <div class="option__item">
                <label class="option__label"><?php _e('Затемнение фона', 'knife-theme'); ?></label>

                <?php
                    printf('<input class="option__range option__range--shadow" type="range" name="%1$s" min="0" max="100" step="5" value="%2$s">',
                        $this->meta . '-shadow',
                        absint($options['shadow'])
                    );
                ?>
            </div>

            <div class="option__item">
                <label class="option__label"><?php _e('Размытие фона', 'knife-theme'); ?></label>

                <?php
                    printf('<input class="option__range option__range--blur" type="range" name="%1$s" min="0" max="10" step="1" value="%2$s">',
                        $this->meta . '-blur',
                        absint($options['blur'])
                    );
                ?>
            </div>
        </div>
    </div>

</div>
