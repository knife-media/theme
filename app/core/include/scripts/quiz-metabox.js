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
  var updateEditor = function(el, media, cl) {
    var options = {
      tinymce: true,
      quicktags: true,
      mediaButtons: media,
      tinymce: {
        toolbar1: 'bold,italic,bullist,numlist,link'
      }
    }

    if(typeof cl !== 'undefined') {
      el.addClass(cl);
    }

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
  }


  /**
   * Add new question
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();
    var last = box.find('.item:last').after(item);

    // Update question title number
    var number = last.find('.item__title > span').text();
    item.find('.item__title > span').html(parseInt(number) + 1);

    // Init wp-editor
    updateEditor(item.find('.item__question'), true, 'item__question--loaded');
  });


  /**
   * Swap questions
   */
  box.on('click', '.item > .dashicons-arrow-up-alt', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Don't swap first element
    if(item.index() === 1) {
      return false;
    }

    var prev = item.prev().insertAfter(item);

    // Update question title number
    var prevNumber = prev.find('.item__title > span').text();
    var itemNumber = item.find('.item__title > span').text();

    prev.find('.item__title > span').text(itemNumber);
    item.find('.item__title > span').text(prevNumber);

    // Re-init wp-editor
    updateEditor(prev.find('.item__question'), true, 'item__question--loaded');
  });


  /**
   * Remove question
   */
  box.on('click', '.item > .dashicons-trash', function(e) {
    e.preventDefault();

    $(this).closest('.item').remove();

    // Update all questions title
    box.find('.item').each(function(i) {
      $(this).find('.item__title > span').text(i);
    });
  });


  /**
   * Init questions editors on load
   */
  box.find('.item').each(function() {
    // Skip first item
    if($(this).index() > 0) {
      updateEditor($(this).find('.item__question'), true, 'item__question--loaded');
    }
  });
});
