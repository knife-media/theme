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
        toolbar1: 'bold,italic,link'
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
   * Update results order
   */
  function sortResults(callback) {
    if(typeof knife_quiz_metabox.meta_results === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_results = knife_quiz_metabox.meta_results;

    box.find('.result:not(:first)').each(function(i) {
      var result = $(this);

      // Update question title
      result.find('.result__title > span').text(i + 1);

      // Change fields name
      result.find('[data-result]').each(function() {
        var data = $(this).data('result');

        // Create name attribute
        var name = meta_results + '[' + i + ']';

        return $(this).attr('name', name + '[' + data + ']');
      });
    });

    if(typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Image append
   */
  function appendImage(poster, attachment, media, thumbnail) {
    if(typeof knife_quiz_metabox.choose === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    // Open default wp.media image frame
    var frame = wp.media({
      title: knife_quiz_metabox.choose,
      multiple: false
    });


    // On open frame select current attachment
    frame.on('open', function() {
      var selection = frame.state().get('selection');
      var current = poster.find('.' + attachment).val();

      return selection.add(wp.media.attachment(current));
    });


    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden inputs values
      poster.find('.' + media).val(selection.url);
      poster.find('.' + attachment).val(selection.id);

      if(thumbnail && typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {src: selection.url, class: media});

      return showcase.prependTo(poster);
    });

    return frame.open();
  }


  /**
   * Toggle answer class by option
   */
  function toggleAnswer(option) {
    // Get new answer class
    var toggle = 'answer--' + option.data('manage-answer');

    box.find('.answer').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Manage checkbox answer trigger
   */
  box.on('change', '.manage input[data-manage-answer]', function() {
    var option = $(this);

    return toggleAnswer(option);
  });


  /**
   * Add new question
   */
  box.on('click', '.action--item', function(e) {
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
   * Choose answer image
   */
  box.on('click', '.answer__poster', function() {
    e.preventDefault();

    var poster = $(this);

    return appendImage(poster, 'answer__poster-attachment', 'answer__poster-media', true);
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
   * Add new result
   */
  box.on('click', '.action--result', function(e) {
    e.preventDefault();

    var result = box.find('.result:first').clone();

    // Insert after last item
    box.find('.result:last').after(result);

    // Sort questions and update editor
    return sortResults(function() {
      updateEditor(result);
    });
  });


  /**
   * Remove result
   */
  box.on('click', '.result__delete', function(e) {
    e.preventDefault();

    var result = $(this).closest('.result');

    // Remove item
    result.remove();

    // Sort questions and update editor
    return sortResults(function() {
      updateEditor(result);
    });
  });


  /**
   * Choose result poster
   */
  box.on('click', '.result__image-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    return appendImage(poster, 'result__image-attachment', 'result__image-media', false);
  });


  /**
   * Onload set up
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


    // Sort results and update editor
    sortResults(function() {
      box.find('.result:not(:first)').each(function(i) {
        var result = $(this);

        // Update editor
        updateEditor(result);
      });
    });


    // Set answer classes
    box.find('.manage input[data-manage-answer]').each(function() {
      var option = $(this);

      // Toggle answer class using option
      toggleAnswer(option);
    });


    // Init summary editors
    updateEditor(box.find('.summary'));


    // Show items
    box.find('.box--items').addClass('box--expand');
  })();
});
