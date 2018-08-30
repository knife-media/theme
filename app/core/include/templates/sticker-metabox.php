<div id="knife-sticker-box" data-ajaxurl="<?php echo admin_url('admin-ajax.php') ?>" data-post="<?php echo get_the_ID() ?>">
  <?php $sticker = get_post_meta(get_the_ID(), self::$meta, true); ?>

  <?php if($sticker) : ?>
    <img id="knife-sticker-image" src="<?php echo $sticker; ?>">
  <?php endif; ?>

  <p class="howto"><?php _e('Загрузите квадратное изображение в формате png', 'knife-theme'); ?></p>

  <fieldset>
    <a id="knife-sticker-delete" href="#delete"><?php _e('Удалить стикер', 'knife-theme'); ?></a>
    <button id="knife-sticker-upload" class="button"><?php _e('Загрузить', 'knife-theme'); ?></button>

    <span class="spinner"></span>
  </fieldset>
</div>
