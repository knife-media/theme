jQuery(document).ready(function($) {
  /**
   * Remove editor on metabox sort start
   */
  $('.meta-box-sortables').on('sortstart', function(e, ui) {
    $(ui.item).find('.wp-editor-area[id]').each(function() {
      var editor = $(this).attr('id');

      if($(this).parents('.tmce-active').length) {
        tinymce.EditorManager.execCommand('mceRemoveEditor', true, editor);
      }
    });
  });


  /**
   * Recreate editor on metabox sort stop
   */
  jQuery('.meta-box-sortables').on('sortstop', function(e, ui) {
    $(ui.item).find('.wp-editor-area[id]').each(function() {
      var editor = $(this).attr('id');

      if($(this).parents('.tmce-active').length) {
        tinymce.EditorManager.execCommand('mceAddEditor', true, editor);
      }
    });
  });
});
