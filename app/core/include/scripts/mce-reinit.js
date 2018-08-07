jQuery(document).ready(function($) {
  /**
   * Remove editor on metabox sort start
   */
  $('.meta-box-sortables').on('sortstart', function() {
    $(this).find('.wp-editor-area[id]').each(function() {
      tinymce.EditorManager.execCommand('mceRemoveEditor', true, $(this).attr('id'));
    });
  });


  /**
   * Recreated editor on metabox sort stop
   */
  jQuery('.meta-box-sortables').on('sortstop', function() {
    $(this).find('.wp-editor-area[id]').each(function() {
      tinymce.EditorManager.execCommand('mceAddEditor', true, $(this).attr('id'));
    });
  });
});
