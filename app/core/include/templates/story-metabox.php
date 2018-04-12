<div id="knife-story-box">
    <?php
        $stories = get_post_meta(get_the_ID(), $this->slug);

        if(count($stories) < 1) {
            $stories[] = ['text' => '', 'image' => '', 'color' => 0];
        }
    ?>

    <?php foreach($stories as $story) : ?>
        <div class="item">
            <?php
                printf('<input class="item__image" type="hidden" name="%s" value="%s">',
                    $this->slug . '[][image]',
                    $story['image'] ?? ''
                );

                printf('<input class="item__color" type="hidden" name="%s" value="%s">',
                    $this->slug . '[][color]',
                    $story['color'] ?? ''
                );

                printf('<textarea class="item__text" name="%1$s" placeholder="%3$s">%2$s</textarea>',
                    $this->slug . '[][text]',
                    $story['text'] ?? '',
                    __('Введите текст истории', 'knife-theme')
                );
            ?>

            <figure class="item__display">
                <?php if(!empty($story['image'])) : ?>
                    <img class="item__display-image" src="<?php echo $story['image']; ?>" alt="">
                <?php endif; ?>

                <figcaption class="item__display-blank"><?php _e('Выбрать фон слайда', 'knife-theme'); ?></figcaption>
            </figure>

            <div class="item__field">
                <span class="item__field-drag"></span>

                <span class="item__field-clone" title="<?php _e('Клонировать фон первого слайда', 'knife-theme'); ?>"></span>
                <span class="item__field-color" title="<?php _e('Изменить цвет шрифта', 'knife-theme'); ?>"></span>

                <span class="item__field-trash" title="<?php _e('Удалить слайд', 'knife-theme'); ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="actions">
        <button class="actions__add button"><?php _e('Добавить слайд в историю'); ?></button>
    </div>

    <div class="options">
        <button class="options__image button"><?php _e('Добавить фон истории'); ?></button>
    </div>

</div>
