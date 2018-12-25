jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-quiz-box');


  /**
   * Use this variable to store for current editor id
   */
  box.data('editor', 1);


  /**
   * Update wp.editor using editorId
   */
  var updateEditor = function(el, cl, options) {
    $.each(el.find('.wp-editor-area'), function(i, editor) {
      var textarea = $(editor);
      var editorId = textarea.attr('id');

      if(typeof editorId === 'undefined') {
        editorId = 'knife-quiz-editor-' + box.data('editor');
        textarea.attr('id', editorId);

        box.data().editor++;
      } else {
        wp.editor.remove(editorId);
      }

      wp.editor.initialize(editorId, options);
    });

    return el.addClass(cl);
  }


  /**
   * Expand and collapse item
   */
  var toggleItem = function(el) {
  }


  /**
   * Init question wp editors
   */
  box.find('.question').each(function(i, el) {
    var options = {
      tinymce: true,
      quicktags: true,
      mediaButtons: true,
      tinymce: {
        toolbar1: 'bold,italic,bullist,numlist,link'
      }
    }

    return updateEditor($(this), 'question--loaded', options);
  });


  /**
   * Init answers wp editors
   */
  box.find('.answer').each(function(i, el) {
    var options = {
      tinymce: true,
      quicktags: true,
      mediaButtons: false,
      tinymce: {
        toolbar1: 'bold,italic,bullist,numlist,link'
      }
    }

    return updateEditor($(this), 'answer--loaded', options);
  });


  /**
   * Collapse questions on load
   */
  box.on('click', '.item__toggle', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    if(item.hasClass('item--expand') && typeof tinyMCE !== 'undefined') {
      tinyMCE.triggerSave();

      var field = item.find('.question__field');
      var value = $(field.val()).text();

      item.find('.question__shortcut').text(value);
    }

    return item.toggleClass('item--expand');
  });


  /**
   * Add new question
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();


  });
});
