<div id="knife-story-box">
    <?php
        $stories = get_post_meta(get_the_ID(), $this->slug);

        if(count($stories) < 1) {
            $stories[] = ['text' => '', 'image' => '', 'color' => 0];
        }
    ?>

    <div class="box box--options">
        <div class="option option--excerpt">
            <label class="option__label"><?php _e('Описание на первом слайде', 'knife-theme'); ?></label>

            <textarea class="option__excerpt"></textarea>
        </div>

        <div class="option option--background">
            <label class="option__label"><?php _e('Фон всех слайдов', 'knife-theme'); ?></label>

            <figure class="option__background">
                <img class="option__background-image" src="https://picsum.photos/300/200" alt="">

                <figcaption class="option__background-blank"><?php _e('Выбрать изображение', 'knife-theme'); ?></figcaption>
            </figure>
        </div>
    </div>

    <div class="box box--items">
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

            <div class="item__field">
                <span class="item__field-drag"></span>

                <span class="item__field-clone" title="<?php _e('Клонировать фон первого слайда', 'knife-theme'); ?>"></span>
                <span class="item__field-color" title="<?php _e('Изменить цвет шрифта', 'knife-theme'); ?>"></span>

                <span class="item__field-trash" title="<?php _e('Удалить слайд', 'knife-theme'); ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="box box--actions">
        <button class="actions__add button"><?php _e('Добавить слайд в историю'); ?></button>
    </div>
</div>
