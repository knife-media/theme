<div id="knife-story-box">
    <?php
        // get stories array
        $stories = get_post_meta(get_the_ID(), $this->meta . '-stories');

        // get options
        $options = [
			'background' => get_post_meta(get_the_ID(), $this->meta . '-background', true) ?? '',
			'excerpt'    => get_post_meta(get_the_ID(), $this->meta . '-excerpt', true) ?? '',
			'shadow'     => get_post_meta(get_the_ID(), $this->meta . '-shadow', true) ?? 0
		];

        if(count($stories) < 1) {
            $stories[] = ['text' => '', 'image' => ''];
        }
    ?>

    <div class="box box--options">
    	<div class="option option--background">
            <label class="option__label"><?php _e('Фон всех слайдов', 'knife-theme'); ?></label>

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

            <div class="option__shadow">
				<?php
					printf('<input class="option__range" type="range" name="%1$s" min="0" max="100" step="5" value="%2$s">',
						$this->meta . '-shadow',
						$options['shadow']
					);
				?>
            </div>
        </div>

		<div class="option option--excerpt">
            <label class="option__label"><?php _e('Описание на первом слайде', 'knife-theme'); ?></label>

			<?php
				printf('<textarea class="option__excerpt" name="%1$s">%2$s</textarea>',
                    $this->meta . '-excerpt',
                    $options['excerpt']
                );
			?>

            <p class="option__howto howto"><?php _e('Текст появится на первом слайде под именем автора', 'knife-theme'); ?></p>
        </div>
    </div>

    <div class="box box--items">
    <?php foreach($stories as $story) : ?>
        <div class="item">
            <?php
				if(!empty($story['image'])) {
					printf('<img class="item__image" src="%s" alt="">', esc_url($story['image']));
				}

                printf('<textarea data-form="text" class="item__text" name="%1$s" placeholder="%3$s">%2$s</textarea>',
                    $this->meta . '-stories[][text]',
                    $story['text'] ?? '',
                    __('Введите текст истории', 'knife-theme')
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
</div>
