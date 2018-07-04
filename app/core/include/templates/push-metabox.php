<div id="knife-push-box" data-ajaxurl="<?php echo admin_url('admin-ajax.php') ?>" data-post="<?php echo get_the_ID() ?>" style="margin-bottom: -10px;">
    <?php
        $opts = get_option($this->option);
        $push = get_post_meta(get_the_ID(), $this->meta, true);

        $title = empty($opts['title']) ? __('Новая статья', 'knife-theme') : $opts['title'];
    ?>

    <?php if(!empty($push)) : ?>
    <p class="howto">
        <?php _e('<strong>Внимание:</strong> Пост уже был запушен', 'knife-theme'); ?>
    </p>
    <?php endif; ?>

     <p>
        <label for="knife-push-title"><strong><?php _e('Заголовок', 'knife-theme') ?></strong></label>
        <input id="knife-push-title" class="widefat" value="<?php echo $title; ?>">
    </p>

    <p>
        <label for="knife-push-message"><strong><?php _e('Сообщение', 'knife-theme') ?></strong></label>
        <textarea id="knife-push-message" class="widefat" rows="4"><?php echo get_the_title(); ?></textarea>
    </p>

    <p>
        <a id="knife-push-send" href="#push-send" class="button"><?php _e('Отправить', 'knife-theme'); ?></a>
        <span class="spinner"></span>
    </p>
</div>
