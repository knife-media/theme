jQuery(document).ready(function($) {
  if(typeof wp === 'undefined' || typeof wp.media === 'undefined') {
    return false;
  }

  var box = $('#knife-quiz-box');


  /**
   * Check required metabox options
   */
  if(typeof knife_quiz_metabox === 'undefined' || typeof knife_quiz_metabox.error === 'undefined') {
    return false;
  }


  /**
   * Use this variable to store for current editor id
   */
  box.data('editor', 1);


  /**
   * Update wp.editor using editorId
   */
  function updateEditor(el) {
    var options = {
      tinymce: true,
      quicktags: true,
      mediaButtons: true,
      tinymce: {
        toolbar1: 'bold,italic,bullist,numlist,link'
      }
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
   * Update questions order
   */
  function sortQuestions(callback) {
    if(typeof knife_quiz_metabox.meta_items === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_items = knife_quiz_metabox.meta_items;

    box.find('.item:not(:first)').each(function(i) {
      var item = $(this);

      // Update question title
      item.find('.item__title > span').text(i + 1);

      // Change fields name
      item.find('[data-item]').each(function() {
        var data = $(this).data('item');

        // Create name attribute
        var name = meta_items + '[' + i + ']';

        return $(this).attr('name', name + '[' + data + ']');
      });

      item.data('number', i);
    });

    if(typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Update answers order
   */
  function sortAnswers(item) {
    if(typeof knife_quiz_metabox.meta_items === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_items = knife_quiz_metabox.meta_items;

    // Get item number
    var number = item.data('number');

    $.each(item.find('.answer:not(:first)'), function(i) {
      var answer = $(this);

      // Loop through answers fields
      answer.find('[data-answer]').each(function() {
        var data = $(this).data('answer');

        // Create name attribute
        var name = meta_items;

        $.each([number, 'answer', i, data], function(i, v) {
          name = name + '[' + v + ']';
        });

        return $(this).attr('name', name);
      });
    });
  }


  /**
   * Add new question
   */
  box.on('click', '.actions__add', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    // Insert after last item
    box.find('.item:last').after(item);

    // Sort questions and update editor
    return sortQuestions(function() {
      updateEditor(item);
    });
  });


  /**
   * Swap questions
   */
  box.on('click', '.item__change', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Don't swap first element
    if(item.index() === 1) {
      return false;
    }

    var prev = item.prev().insertAfter(item);

    // Sort questions and update editor
    return sortQuestions(function() {
      updateEditor(prev);
    });
  });


  /**
   * Remove question
   */
  box.on('click', '.item__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Remove item
    item.remove();

    // Sort questions and update editor
    return sortQuestions(function() {
      updateEditor(item);
    });
  });


  /**
   * Add answer to question
   */
  box.on('click', '.item__manage-add', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Clone fake answer
    var answer = item.find('.answer:first').clone();

    // Insert after last answer
    item.find('.answer:last').after(answer);

    // Show answers button
    item.find('.item__manage-toggle').attr('disabled', false);

    if(!item.find('.item__answers').hasClass('item__answers--expand')) {
      item.find('.item__manage-toggle').trigger('click');
    }

    return sortAnswers(item);
  });


  /**
   * Remove answer
   */
  box.on('click', '.answer__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Remove answer
    $(this).closest('.answer').remove();

    // Toggle answers button
    item.find('.item__manage-toggle').attr('disabled',
      item.find('.answer').length <= 1
    );

    return sortAnswers(item);
  });


  /**
   * Toggle answers
   */
  box.on('click', '.item__manage-toggle', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    // Swap title with data attr
    var title = $(this).text();

    $(this).text($(this).data('toggle'))
    $(this).data('toggle', title);

    item.find('.item__answers').toggleClass('item__answers--expand')
  });


  /**
   * Init items on load
   */
  (function() {
    // Sort questions and update editor
    sortQuestions(function() {
      box.find('.item:not(:first)').each(function(i) {
        var item = $(this);

        // Sort answers
        sortAnswers(item);

        // Update editor
        updateEditor(item);

        // Toggle answers button
        item.find('.item__manage-toggle').attr('disabled',
          item.find('.answer').length <= 1
        );
      });
    });

    // Show items
    box.find('.box').addClass('box--expand');
  })();
});
