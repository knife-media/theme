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
   * Add class for short time
   */
  function blinkClass(element, cl) {
    element.addClass(cl).delay(500).queue(function(){
      element.removeClass(cl).dequeue();
    });
  }


  /**
   * Show generate loader
   */
  function toggleLoader(result) {
    var spinner = result.find('.result__image-spinner');

    spinner.toggleClass('is-active');

    result.find('.result__image-generate').prop('disabled',
      result.hasClass('is-active')
    );
  }


  /**
   * Update poster fields
   */
  function updatePosters(result, posters) {
    var points = {};

    $.each(['from', 'to'], function(i, v) {
      points[v] = result.find('[data-result="' + v + '"]').val();
    });

    // Collect all results posters by points
    if(typeof posters === 'undefined') {
      posters = {};

      result.find('input[data-poster]').each(function() {
        var score = $(this).data('poster');

        posters[score] = $(this).val();
      });
    }

    // Clone first field
    var clone = result.find('.poster:first').clone();

    if(points.from > points.to) {
      return false;
    }

    // Clear posters to recreate them below
    result.find('.result__posters').empty();

    for(var i = points.from; i <= points.to; i++) {
      var poster = clone.clone();

      // Set points in title
      poster.find('.poster__title > span').text(i);

      // Set input attributes
      poster.find('.poster__field').attr({
        'value': '', 'data-poster': i
      });

      if(posters.hasOwnProperty(i)) {
        poster.find('.poster__field').val(posters[i]);
      }

      result.find('.result__posters').append(poster);
    }

    sortPosters(result);
  }


  /**
   * Generate poster using ajax
   */
  function createPoster(result) {
    var data = {
      'action': knife_quiz_metabox.action,
      'nonce': knife_quiz_metabox.nonce,
      'post_id': knife_quiz_metabox.post_id,
    }

    // Add required poster fields
    $.each(['heading', 'description', 'attachment'], function(i, v) {
      data[v] = result.find('[data-result="' + v + '"]').val();
    });

    // Add achievment poster info if need
    if(result.hasClass('result--achievment')) {
      $.each(['achievment', 'from', 'to'], function(i, v) {
        data[v] = result.find('[data-result="' + v + '"]').val();
      });
    }

    // Add quiz title if exists
    if($("#title").length > 0) {
      data.title = $('#title').val();
    }


//    result.find('.result__warning').html('').hide();

    var xhr = $.ajax({method: 'POST', url: ajaxurl, data: data}, 'json');

    xhr.done(function(answer) {
      if(answer.data.length > 1 && answer.success === true) {
        result.find('[data-result="media"]').val(answer.data);
        result.find('.result__image-poster > img').attr('src', answer.data);

        return toggleLoader(result);
      }

      if(answer.data.length > 1 && answer.success === false) {
        result.find('.result__warning').html(answer.data).show();

        return toggleLoader(result);
      }

      result.find('.result__warning').html(knife_quiz_metabox.error).show();

      return toggleLoader(result);
    });

    xhr.error(function() {
      toggleLoader(result);

      result.find('.result__warning').html(knife_quiz_metabox.error).show();
    });

    return toggleLoader(result);
  }


  /**
   * Update wp.editor using editorId
   */
  function updateEditor(el) {
    if(typeof knife_quiz_metabox.choose === 'undefined') {
      knife_quiz_metabox.editor = 'html';
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

      wp.editor.initialize(editorId, {
        tinymce: {
          toolbar1: 'link',
          init_instance_callback: function() {
            if(window.tinymce && window.switchEditors) {
              window.switchEditors.go(editorId, knife_quiz_metabox.editor);
            }
          }
        },
        quicktags: {
          buttons: 'link'
        },
        mediaButtons: true
      });
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

        $(this).attr('name', name + '[' + data + ']');
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

        $.each([number, 'answers', i, data], function(j, v) {
          name = name + '[' + v + ']';
        });

        $(this).attr('name', name);
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

        $(this).attr('name', name + '[' + data + ']');
      });

      result.data('number', i);
    });

    if(typeof callback === 'function') {
      return callback();
    }
  }


  /**
   * Update posters order
   */
  function sortPosters(result) {
    if(typeof knife_quiz_metabox.meta_results === 'undefined') {
      return alert(knife_quiz_metabox.error);
    }

    var meta_results = knife_quiz_metabox.meta_results;

    // Get item number
    var number = result.data('number');

    // Loop through answers fields
    result.find('[data-poster]').each(function() {
      var data = $(this).data('poster');

      // Create name attribute
      var name = meta_results;

      $.each([number, 'posters', data], function(i, v) {
        name = name + '[' + v + ']';
      });

      $(this).attr('name', name);
    });
  }


  /**
   * Image append
   */
  function appendImage(poster, attachment, thumbnail, media) {
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
      var current = poster.find(attachment).val();

      return selection.add(wp.media.attachment(current));
    });


    // On image select
    frame.on('select', function() {
      var selection = frame.state().get('selection').first().toJSON();

      // Set hidden inputs values
      if(typeof media !== 'undefuned') {
        poster.find(media).val(selection.url);
      }

      poster.find(attachment).val(selection.id);

      // Set thumbnail as selection if exists
      if(thumbnail && typeof selection.sizes.thumbnail !== 'undefined') {
        selection = selection.sizes.thumbnail;
      }

      if(poster.find('img').length > 0) {
        return poster.find('img').attr('src', selection.url);
      }

      var showcase = $('<img />', {src: selection.url});

      return showcase.prependTo(poster);
    });

    frame.open();
  }


  /**
   * Toggle manage options
   */
  function toggleManage(option) {
    // Get new answer class
    var toggle = 'answer--' + option.data('manage');

    box.find('.answer').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Toggle summary options
   */
  function toggleSummary(option) {
    var toggle = 'result--' + option.data('summary');

    box.find('.result').toggleClass(toggle,
      option.is(':checked')
    );
  }


  /**
   * Toggle details options
   */
  function toggleDetails(option) {
    box.find('.result').toggleClass('result--details',
      option.data('details') === 'result' && option.is(':checked')
    );

    box.find('.remark').toggleClass('remark--details',
      option.data('details') === 'remark' && option.is(':checked')
    );
  }


  /**
   * Manage checkbox answer trigger
   */
  box.on('change', '.manage input[data-manage]', function() {
    var option = $(this);

    return toggleManage(option);
  });


  /**
   * Summary checkbox trigger
   */
  box.on('change', '.summary input[data-summary]', function() {
    var option = $(this);

    return toggleSummary(option);
  });


  /**
   * Details radio trigger
   */
  box.on('change', '.summary input[data-details]', function() {
    var option = $(this);

    return toggleDetails(option);
  });


  /**
   * Add new question
   */
  box.on('click', '.action--item', function(e) {
    e.preventDefault();

    var item = box.find('.item:first').clone();

    // Insert after last item
    box.find('.item:last').after(item);

    sortQuestions(function() {
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

    sortQuestions(function() {
      // Sort answers
      box.find('.item:not(:first)').each(function(i) {
        sortAnswers($(this));
      });

      // Update item editor
      updateEditor(item);
    });
  });


  /**
   * Remove question
   */
  box.on('click', '.item__delete', function(e) {
    e.preventDefault();

    var item = $(this).closest('.item');

    sortQuestions(function() {
      // Sort answers
      box.find('.item:not(:first)').each(function(i) {
        sortAnswers($(this));
      });

      // Update item editor
      updateEditor(item);
    });

    item.remove();
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

    sortAnswers(item);
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

    sortAnswers(item);
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
  box.on('click', '.answer__poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    appendImage(poster, '.answer__poster-attachment', true);
  });


  /**
   * Add new result
   */
  box.on('click', '.action--result', function(e) {
    e.preventDefault();

    var result = box.find('.result:first').clone();

    // Insert after last item
    box.find('.result:last').after(result);

    sortResults(function() {
      // Sort posters
      box.find('.result:not(:first)').each(function(i) {
        sortPosters($(this));
      });

      // Update item editor
      updateEditor(result);
    });
  });


  /**
   * Remove result
   */
  box.on('click', '.result__delete', function(e) {
    e.preventDefault();

    var result = $(this).closest('.result');

    sortResults(function() {
      // Sort posters
      box.find('.result:not(:first)').each(function(i) {
        sortPosters($(this));
      });

      // Update item editor
      updateEditor(result);
    });

    result.remove();
  });


  /**
   * Choose result poster
   */
  box.on('click', '.result__image-poster', function(e) {
    e.preventDefault();

    var poster = $(this);

    appendImage(poster, '.result__image-attachment', false, '.result__image-media');
  });


  /**
   * Generate button click
   */
  box.on('click', '.result__image-generate', function(e) {
    e.preventDefault();

    var result = $(this).closest('.result');
    var caption = result.find('.result__image-caption');

    if(result.find('.result__image-poster > img').length < 1) {
      return blinkClass(caption, 'result__image-caption--error');
    }

    createPoster(result);
  });


  /**
   * Show posters fields
   */
  box.on('click', '.result__image-manual', function(e) {
    e.preventDefault();

    var result = $(this).closest('.result');

    if(result.find('.result__posters').children().length === 0) {
      return alert(knife_quiz_metabox.error);
    }

    if(!result.hasClass('result--posters')) {
      updatePosters(result);
    }

    result.toggleClass('result--posters');
  });


  /**
   * Display poster field
   */
  box.on('click', '.poster__display', function(e) {
    e.preventDefault();

    var field = $(this).siblings('.poster__field').val();

    if(field.length === 0) {
      return false;
    }

    var poster = $(this).closest('.result').find('.result__image-poster');

    if(poster.find('img').length > 0) {
      return poster.find('img').attr('src', field);
    }

    var showcase = $('<img />', {src: field});

    return showcase.prependTo(poster);
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

        // Sort posters
        sortPosters(result);

        // Update editor
        updateEditor(result);
      });
    });


    // Set manage classes
    box.find('.manage input[data-manage]').each(function() {
      var option = $(this);

      toggleManage(option);
    });


    // Set summary classes
    box.find('.summary input[data-summary]').each(function() {
      var option = $(this);

      toggleSummary(option);
    });


    // Set details classes
    box.find('.summary input[data-details]:checked').each(function() {
      var option = $(this);

      toggleDetails(option);
    });


    // Init remark editor
    updateEditor(box.find('.remark'));

    // Show items
    box.find('.box--items').addClass('box--expand');

    // Show results
    box.find('.box--results').addClass('box--expand');
  })();
});
